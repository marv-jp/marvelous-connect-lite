<?php

/**
 * Logic_Payment_Trade_CurrencyPaymentクラスのファイル
 *
 * Logic_Payment_Trade_CurrencyPaymentクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */

/**
 * Logic_Payment_Trade_CurrencyPayment
 *
 * 仮想通貨アクティビティ図：「通貨消費」
 *
 * アプリケーションユーザの消費処理を行うクラス
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */
class Logic_Payment_Trade_CurrencyPayment extends Logic_Payment_Trade_CurrencyAbstract
{
    /**
     * 有償分で払いきれていない残額
     *
     * @var int
     */
    private $_remainingAmount = 0;

    /**
     * exec返却用コレクション
     *
     * @var Misp_Collection_ApplicationUserCurrency アプリケーションユーザ通貨コレクション
     */
    private $_responseCollection = null;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * 初期化
     */
    public function init()
    {
        $this->_responseCollection = new Misp_Collection_ApplicationUserCurrency();
        $this->_responseCollection->shouldDbLoggingOff();
    }

    /**
     * 実処理
     * 
     * この処理のトランザクションは呼び出し側に依存しています。
     *
     * @return Misp_Collection_ApplicationUserCurrency
     * @throws Common_Exception_DataInconsistencies 消費しようとしたレコードが償却処理(バッチ)によって削除され、データ不整合が発生した場合にThrowされます
     */
    public function exec()
    {
        // 初期化
        $this->init();

        $applicationUserPayment             = $this->getApplicationUserPayment();
        $applicationUserCurrencyPaymentItem = $this->getApplicationUserCurrencyPaymentItem();

        // 処理パラメータをモデルから取り出す
        $applicationUserId      = $applicationUserPayment->getApplicationUserId();
        $applicationId          = $applicationUserPayment->getApplicationId();
        $applicationWorldId     = $applicationUserPayment->getApplicationWorldId();
        $paymentPlatformId      = $applicationUserPayment->getPaymentPlatformId();
        $applicationCurrencyId  = $applicationUserCurrencyPaymentItem->getApplicationCurrencyId();
        $paymentDeviceId        = $applicationUserPayment->getPaymentDeviceId();
        $paymentRatingId        = $applicationUserPayment->getPaymentRatingId();
        $this->_remainingAmount = $applicationUserCurrencyPaymentItem->getCurrencyAmount();
        // 消費通貨順配列を取得
        $currencySequence       = $this->getConfigCurrencyPaymentSequenceWithPaidWith($applicationId, $paymentPlatformId, $paymentDeviceId, $paymentRatingId);

        // パラメータチェック
        //   必須
        $this->_isValidateValue($applicationUserId);
        $this->_isValidateValue($applicationId);
        $this->_isValidateValue($applicationCurrencyId);
        $this->_isValidateValue($paymentPlatformId);
        $this->_isValidateValue($this->_remainingAmount);
        //   任意(長さのみチェック)
        $this->_isValidateLength($applicationWorldId);

        try {

            // 通貨使用順の設定にもとづいて処理を行っていく
            foreach ($currencySequence as $procLabel) {

                switch ($procLabel) {

                    // 有償通貨
                    case Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_CREDIT:

                        $this->_proc($this->_readApplicationUserCurrencyForCredit());
                        break;

                    // プラットフォーム固有無償通貨
                    case Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_PF_BONUS:

                        $this->_proc($this->_readApplicationUserCurrencyForBonus(Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_PF_BONUS));
                        break;

                    // プラットフォーム共通無償通貨
                    case Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_BONUS:

                        $this->_proc($this->_readApplicationUserCurrencyForBonus(Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_BONUS));
                        break;

                    default:
                        break;
                }

                // 払いきれた場合はループを終了する
                if (!$this->_isRemainingAmount()) {
                    break;
                }
            }

            // 処理後に払いきれていない残額がある場合はあり得ないので、残高不足として例外をあげる
            if ($this->_isRemainingAmount()) {
                throw new Common_Exception_InsufficientFunds(Logic_Payment_Const::MSG_INSUFFICIENTFUNDS . $this->_generateModelLogFormat($applicationUserPayment) . $this->_generateModelLogFormat($applicationUserCurrencyPaymentItem));
            }
            //
        } catch (Common_Exception_NotModified $e) {
            throw new Common_Exception_DataInconsistencies($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return $this->_responseCollection;
    }

    /**
     * 共通通貨処理(「通貨消費」シート：緑破線枠部分)
     * 
     * execの実処理
     * 
     * @param Application_Model_ApplicationUserCurrency[] $applicationUserCurrencies
     * @throws Common_Exception_NotModified 更新対象が存在しなかった場合にThrowされます
     * @throws Common_Exception_NotModified 削除対象が存在しなかった場合にThrowされます
     */
    private function _proc($applicationUserCurrencies)
    {
        // 手持ちの通貨額レコード分、消費処理を行う
        foreach ($applicationUserCurrencies as $applicationUserCurrency) {

            // 残額計算
            //   (手持ちの通貨額で払いきれるかを計算)
            $balance = Logic_Payment_Calculator::calcBalance($applicationUserCurrency->getCurrencyAmount(), $this->_remainingAmount);

            // 足りた場合
            if ($this->_isBalanceEnough($balance)) {

                // 更新情報作成
                $updateApplicationUserCurrency = new Application_Model_ApplicationUserCurrency($applicationUserCurrency);

                // 処理中モデルの更新
                // 通貨額だけ「消費対象通貨額」に変更する
                $updateApplicationUserCurrency->setCurrencyAmount($this->_remainingAmount);

                // 消費対象額をゼロにする
                $this->_remainingAmount = 0;

                // 通貨減額処理
                $this->_reduceCurrencyProc($updateApplicationUserCurrency, $balance);

                break;
            }

            // ちょうどの場合     
            if ($this->_isBalanceJust($balance)) {

                // 消費対象額をゼロにする
                $this->_remainingAmount = 0;

                // 通貨減額処理
                $this->_reduceCurrencyProc($applicationUserCurrency, $balance);

                break;
            }

            // マイナス(買えない)の場合
            if ($this->_isBalanceNotEnough($balance)) {

                // 消費対象通貨額の再計算
                $this->_remainingAmount = Logic_Payment_Calculator::calcBalance($this->_remainingAmount, $applicationUserCurrency->getCurrencyAmount());

                // 通貨減額処理
                $this->_reduceCurrencyProc($applicationUserCurrency, $balance);

                // 買えない場合は次のモデル処理をするので、breakしない
            }
        }
    }

    /**
     * アプリケーションユーザ通貨更新
     * 
     * アプリケーションユーザ通貨を更新します。
     *
     * @param Application_Model_ApplicationUserCurrency $m 更新内容を含んだモデル
     * @throws Common_Exception_NotModified 更新対象が存在しなかった場合にThrowされます
     */
    private function _updateApplicationUserCurrency(Application_Model_ApplicationUserCurrency $m)
    {
        // 更新PK生成
        $pk = $this->_buildApplicationUserCurrencyUpdatePks($m);

        if (!$this->getApplicationUserCurrencyMapper($this->getDbSectionNameMain())->update($m, $pk['applicationUserPaymentItemId'], $pk['applicationCurrencyId'], $pk['paymentPlatformId'], $pk['paymentDeviceId'], $pk['paymentRatingId'], $pk['applicationUserId'], $pk['applicationId'], $pk['applicationWorldId'], $pk['unitPrice'])) {
            throw new Common_Exception_NotModified(Logic_Payment_Const::LOG_MSG_UPDATE_FAIL . $this->_generateModelLogFormat($m));
        }
    }

    /**
     * アプリケーションユーザ通貨削除
     * 
     * アプリケーションユーザ通貨を削除します。
     *
     * @param Application_Model_ApplicationUserCurrency $m 削除用Where値を含んだモデル
     * @throws Common_Exception_NotModified 削除対象が存在しなかった場合にThrowされます
     */
    private function _deleteApplicationUserCurrency(Application_Model_ApplicationUserCurrency $m)
    {
        //  削除
        if (!$this->getApplicationUserCurrencyMapper($this->getDbSectionNameMain())->delete($m->getApplicationUserPaymentItemId(), $m->getApplicationCurrencyId(), $m->getPaymentPlatformId(), $m->getPaymentDeviceId(), $m->getPaymentRatingId(), $m->getApplicationUserId(), $m->getApplicationId(), $m->getApplicationWorldId(), $m->getUnitPrice())) {
            throw new Common_Exception_NotModified(Logic_Payment_Const::LOG_MSG_DELETE_FAIL . $this->_generateModelLogFormat($m));
        }
    }

    /**
     * アプリケーションユーザ通貨(有償)取得
     * 
     * アプリケーションユーザ通貨(有償)を取得します。
     *
     * @return Application_Model_ApplicationUserCurrency[]
     */
    private function _readApplicationUserCurrencyForCredit()
    {
        $result = $this->_readApplicationUserCurrency(Logic_Payment_Const::MODE_CREDIT);
        if ($result) {
            $this->_responseCollection->shouldDbLoggingOn();
        }

        return $result;
    }

    /**
     * アプリケーションユーザ通貨(無償)取得
     * 
     * アプリケーションユーザ通貨(無償)を取得します。
     *
     * @param string $procLabel 通貨消費順ラベル
     * @return array Application_Model_ApplicationUserCurrency[]
     */
    private function _readApplicationUserCurrencyForBonus($procLabel)
    {
        return $this->_readApplicationUserCurrency(Logic_Payment_Const::MODE_BONUS, $procLabel);
    }

    /**
     * アプリケーションユーザ通貨取得
     * 
     * アプリケーションユーザ通貨を取得します。
     *
     * @param int $mode モード(有償|無償)
     * @param string $procLabel 通貨消費順ラベル
     * @return array Application_Model_ApplicationUserCurrency[]
     */
    private function _readApplicationUserCurrency($mode, $procLabel = '')
    {
        // $procLabelからWHERE条件を設定
        $where = $this->_buildApplicationUserCurrencySelectPks($mode, $procLabel);

        // プラットフォーム共通無償通貨の場合、ソートを行わないで取得する
        if ($procLabel == Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_BONUS) {
            // プラットフォーム共通無償通貨の場合、ソートを行わないで取得する
            return $this->getApplicationUserCurrencyMapper($this->getDbSectionNameMain())->fetchAll($where);
        }

        // 通貨共有グループ取得
        $applicationUserPayment = $this->getApplicationUserPayment();
        $applicationId          = $applicationUserPayment->getApplicationId();
        $platformId             = $applicationUserPayment->getPaymentPlatformId();
        $deviceId               = $applicationUserPayment->getPaymentDeviceId() ?? '';
        $ratingId               = $applicationUserPayment->getPaymentRatingId() ?? '';

        $currencySharingGroup = new Logic_Payment_CurrencySharingGroup();

        // 有償処理の場合は消費順を明確にするため、下記の条件でソートする
        //  有効期限の古いもの
        //  実行日時の古いもの
        // (ボリュームディスカウントによる値引きが今後の事実上の標準となっていく可能性が高く、
        //  単価の異なる通貨が日常的に発生することもあり、また、未使用残高への監査の目が厳しくなっていく)
        if ($mode == Logic_Payment_Const::MODE_CREDIT) {
            $currencySharingGroup->init($applicationId, Logic_Payment_CurrencySharingGroup::MODE_CREDIT);
            return $this->getApplicationUserCurrencyMapper($this->getDbSectionNameMain())->fetchAllOrderNullsLast($where, $currencySharingGroup->get($platformId, $deviceId, $ratingId));
        }

        if ($procLabel == Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_PF_BONUS) {
            $currencySharingGroup->init($applicationId, Logic_Payment_CurrencySharingGroup::MODE_BONUS);
            return $this->getApplicationUserCurrencyMapper($this->getDbSectionNameMain())->fetchAllBySharingGroup($where, $currencySharingGroup->get($platformId, $deviceId, $ratingId));
        }
    }

    /**
     * アプリケーションユーザ通貨検索条件構築
     * 
     * アプリケーションユーザ通貨を更新する際のWhere条件の連想配列を構築します。<br>
     *
     * @param Application_Model_ApplicationUserCurrency $m 更新内容を含んだモデル
     * @return array アプリケーションユーザ通貨を更新する際のWhere条件の連想配列
     */
    private function _buildApplicationUserCurrencyUpdatePks(Application_Model_ApplicationUserCurrency $m)
    {
        return array(
            'applicationUserPaymentItemId' => $m->getApplicationUserPaymentItemId(),
            'applicationCurrencyId'        => $m->getApplicationCurrencyId(),
            'paymentPlatformId'            => $m->getPaymentPlatformId(),
            'paymentDeviceId'              => $m->getPaymentDeviceId(),
            'paymentRatingId'              => $m->getPaymentRatingId(),
            'applicationUserId'            => $m->getApplicationUserId(),
            'applicationId'                => $m->getApplicationId(),
            'applicationWorldId'           => $m->getApplicationWorldId(),
            'unitPrice'                    => $m->getUnitPrice()
        );
    }

    /**
     * アプリケーションユーザ通貨検索条件構築
     * 
     * アプリケーションユーザ通貨を取得する際のWhere条件の連装配列を構築します。<br>
     * <br>
     * 引数($mode)によって、有償レコードまたは無償レコードに分岐します。
     *
     * @param int $mode モード(有償|無償)
     * @param string $procLabel 通貨消費順ラベル
     * @return array アプリケーションユーザ通貨を取得すする際のWhere条件の連想配列
     */
    private function _buildApplicationUserCurrencySelectPks($mode, $procLabel)
    {
        $p  = $this->getApplicationUserPayment();
        $pi = $this->getApplicationUserCurrencyPaymentItem();

        $returnWhere = $this->_adjustUnitPriceForSelect($mode, array(
            'applicationUserId'     => array($p->getApplicationUserId()),
            'applicationId'         => array($p->getApplicationId()),
            'applicationWorldId'    => array($p->getApplicationWorldId()),
            'applicationCurrencyId' => array($pi->getApplicationCurrencyId()),
        ));

        // プラットフォーム共通無償通貨の場合
        // 通貨消費順の設定値でデータ取得条件を調整する
        switch ($procLabel) {

            // プラットフォーム共通無償通貨
            case Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_BONUS:

                // 全体の無償通貨を取得
                $returnWhere['paymentPlatformId'] = array('');
                break;

            default:
                break;
        }

        return $returnWhere;
    }

    /**
     * 要支払い残額判定
     * 
     * 有償分で払いきれていない残額があるかどうかを判定します。
     * 
     * @return boolean
     */
    private function _isRemainingAmount()
    {
        return 0 < $this->_remainingAmount;
    }

    /**
     * 単価検索条件値調整(SELECT用)
     * 
     * @param int $mode モード：有償/無償
     * @param array $returnWhere WHERE条件の連想配列
     * @return mixed
     */
    private function _adjustUnitPriceForSelect($mode, $returnWhere)
    {
        if ($mode == Logic_Payment_Const::MODE_BONUS) {
            $returnWhere['unitPrice']                    = array(Logic_Payment_Const::PRICE_BONUS);
            $returnWhere['applicationUserPaymentItemId'] = array(0);
        } else {
            $returnWhere['unitPrice not'] = array(Logic_Payment_Const::PRICE_BONUS);
        }

        return $returnWhere;
    }

    /**
     * 単価検索条件値調整(UPDATE用)
     * 
     * @param int $mode モード：有償/無償
     * @param Application_Model_ApplicationUserCurrency $m
     * @return boolean
     */
    private function _adjustUnitPriceForUpdate($mode, Application_Model_ApplicationUserCurrency $m)
    {
        return ($mode === Logic_Payment_Const::MODE_CREDIT) ? $m->getUnitPrice() : 0;
    }

    /**
     * 残高確認
     * 
     * 足りていることを判定します。
     * 
     * @param int $balance
     * @return boolean
     */
    private function _isBalanceEnough($balance)
    {
        return (0 < $balance);
    }

    /**
     * 残高確認
     * 
     * 残高が足りていないことを判定します。
     * 
     * @param int $balance
     * @return boolean
     */
    private function _isBalanceNotEnough($balance)
    {
        return (0 > $balance);
    }

    /**
     * 残高確認
     * 
     * 残高が丁度であることを判定します。
     * 
     * @param int $balance
     * @return boolean
     */
    private function _isBalanceJust($balance)
    {
        return (0 === $balance);
    }

    /**
     * 8. 通貨減額処理
     * 
     * @param Application_Model_ApplicationUserCurrency $applicationUserCurrencyModel 消費処理中のアプリケーションユーザ通貨モデル
     * @param int $currencyAmount 残額
     */
    private function _reduceCurrencyProc(Application_Model_ApplicationUserCurrency $applicationUserCurrencyModel, $currencyAmount)
    {
        // データ更新日時生成(現在時刻)
        $nowDatetime = $this->getNowDatetime();

        // 返却情報の追加
        // $applicationUserCurrencyModelは、後続処理で値が変化するため、参照されないようcloneしたものをアタッチする
        $this->_responseCollection->attach(clone $applicationUserCurrencyModel);

        // 足りた場合
        if ($this->_isBalanceEnough($currencyAmount)) {

            // 更新値をセット
            $applicationUserCurrencyModel->setCurrencyAmount($currencyAmount);    // 足りた場合、手持ち分を減算更新しないといけないので、残額計算結果をモデルにセットし、更新をかける
            $applicationUserCurrencyModel->setUpdatedDate($nowDatetime);

            // 消費分の減算
            $this->_updateApplicationUserCurrency($applicationUserCurrencyModel);
        }

        // 0以下の場合
        if ($this->_isBalanceJust($currencyAmount) || $this->_isBalanceNotEnough($currencyAmount)) {

            // 処理レコードを削除する
            $this->_deleteApplicationUserCurrency($applicationUserCurrencyModel);
        }
    }

}
