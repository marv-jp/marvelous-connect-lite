<?php

/**
 * Logic_Payment_Trade_Abstractクラスのファイル
 *
 * Logic_Payment_Trade_Abstractクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */

/**
 * Logic_Payment_Trade_Abstract
 *
 * 仮想通貨取引基底クラス
 *
 * 取引：購入/消費/両替
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */
abstract class Logic_Payment_Trade_Abstract extends Logic_Payment_Abstract implements Logic_Payment_Trade_Interface
{
    /**
     * APIリクエストパラメータの"entry"項目の中身
     * 
     * 各処理で参照するので、プロパティに保持する
     *
     * @var array
     */
    protected $_buildParams = array();

    /**
     * コンストラクタ
     * 
     * 後続の処理に必要なパラメータをプロパティに保持します
     * 
     * @param array $buildParams APIリクエストパラメータの"entry"項目の中身
     */
    public function __construct(array $buildParams)
    {
        $this->_buildParams = $buildParams;
    }

    /**
     * アプリケーションユーザターゲット通貨ペイメントアイテムコレクション構築
     * 
     * APIのリクエストパラメータを使用してアプリケーションユーザターゲット通貨ペイメントアイテムコレクション(とその中身)を構築します<br>
     * <br>
     * 無償通貨のパラメータ(項目名)がある場合はそれもモデル化し、
     * アプリケーションユーザターゲット通貨ペイメントアイテムのプロパティに保持します。
     *
     * @param array $buildParams APIリクエストパラメータの"entry"項目の中身
     * @return Misp_Collection_ApplicationUserTargetCurrencyPaymentItem
     */
    static protected function _buildTargetCurrencyPaymentItemCollection(array $buildParams)
    {

        // コレクション：アプリケーションユーザターゲット通貨ペイメントアイテム
        $pic = new Misp_Collection_ApplicationUserTargetCurrencyPaymentItem();

        // ペイメントアイテムコレクションに詰めるモデル(ペイメントアイテム)のネタがきているか
        if (
                !self::isPlatformDevicePair($buildParams, Logic_Payment_Const::PLATFORM_ID_MOOG, Logic_Payment_Const::DEVICE_ID_BLANK)
        ) {
            // アクティビティ図「仮想通貨情報更新 購入」シート「パラメータ詰替処理」部分
            if (!isset($buildParams['payment']) || 0 === count($buildParams['payment'])) {
                throw new Common_Exception_IllegalParameter('payment項目がありません');
            }

            foreach ($buildParams['payment'] as $p) {

                // アプリケーションユーザターゲット通貨ペイメントアイテムモデルの生成とアタッチ
                $targetCurrencyPaymentItem = new Application_Model_ApplicationUserTargetCurrencyPaymentItem();
                $targetCurrencyPaymentItem->setApplicationCurrencyId((isset($p['toId'])) ? $p['toId'] : null);
                $targetCurrencyPaymentItem->setPlatformProductId((isset($p['productId'])) ? $p['productId'] : null);
                $quantity                  = 1;
                if (array_key_exists('quantity', $p) && $p['quantity'] != 0) {
                    $quantity = $p['quantity'];
                }
                $targetCurrencyPaymentItem->setCurrencyAmount((isset($p['toCurrency'])) ? $p['toCurrency'] * $quantity : null);
                $targetCurrencyPaymentItem->setPrice((isset($p['price'])) ? $p['price'] : null);


                // 無償通貨のパラメータ(項目名)がある場合はそれもモデル化し、
                // アプリケーションユーザターゲット通貨ペイメントアイテムのプロパティに保持
                if (isset($p[Logic_Payment_Const::REQUEST_OBJECT_CREDIT_BONUS])) {

                    // コレクション：アプリケーションユーザターゲット通貨ペイメントアイテム(無償用)
                    $bonusItemCollection = new Misp_Collection_ApplicationUserTargetCurrencyPaymentItem();

                    foreach ($p[Logic_Payment_Const::REQUEST_OBJECT_CREDIT_BONUS] as $bonusItem) {

                        $bonus = new Application_Model_ApplicationUserTargetCurrencyPaymentItem();
                        $bonus->setApplicationCurrencyId((isset($bonusItem['toId'])) ? $bonusItem['toId'] : null);
                        $bonus->setCurrencyAmount((isset($bonusItem['toCurrency'])) ? $bonusItem['toCurrency'] * $quantity : null);
                        $bonusItemCollection->attach($bonus);
                    }

                    $bonusItemCollection->rewind();
                    // モデル：アプリケーションユーザターゲット通貨ペイメントアイテムにコレクション：アプリケーションユーザターゲット通貨ペイメントアイテム(無償用)をセット
                    $targetCurrencyPaymentItem->setTargetCurrencyPaymentItemCollectionForBonus($bonusItemCollection);
                }

                $pic->attach($targetCurrencyPaymentItem);
            }
        }

        $pic->rewind();

        return $pic;
    }

    /**
     * 残高不足チェックのラッパー
     * 
     * アプリケーションユーザ通貨ペイメントアイテムから消費額を加算し、残高不足チェックに流します。
     * 
     * @param Application_Model_ApplicationUserCurrencyPaymentItem[] $applicationUserCurrencyPaymentItems
     * @throws Common_Exception_InsufficientFunds
     */
    protected function _isCurrencyEnoughForUpdate(array $applicationUserCurrencyPaymentItems)
    {
        // 残高不足チェック
        // チェック用配列生成
        $currencyPayment = array();
        foreach ($applicationUserCurrencyPaymentItems as $applicationUserCurrencyPaymentItem) {

            $currencyId     = $applicationUserCurrencyPaymentItem->getApplicationCurrencyId();
            $currencyAmount = $applicationUserCurrencyPaymentItem->getCurrencyAmount();

            // 消費額取得
            if (array_key_exists($currencyId, $currencyPayment)) {
                // 通貨IDがある場合
                $currencyPayment[$currencyId] += $currencyAmount;
            } else {
                // 通貨IDがない場合
                $currencyPayment[$currencyId] = $currencyAmount;
            }
        }
        // 残高不足チェック処理
        if (!$this->_isCurrencyEnough($currencyPayment)) {
            throw new Common_Exception_InsufficientFunds(Logic_Payment_Const::MSG_INSUFFICIENTFUNDS);
        }
    }

    /**
     * アプリケーションユーザ通貨ペイメントアイテム取得
     * 
     * アプリケーションユーザペイメントIDでアプリケーションユーザターゲット通貨ペイメントアイテムを取得し、
     * その情報でアプリケーションユーザ通貨ペイメントアイテムのレコード(from)を取得します。
     * 
     * @return Application_Model_ApplicationUserCurrencyPaymentItem[] アプリケーションユーザ通貨ペイメントアイテムの配列
     */
    protected function _readFromApplicationUserCurrencyPaymentItem()
    {
        $where                       = array('applicationUserPaymentId' => array($this->getApplicationUserPayment()->getApplicationUserPaymentId()));
        $applicationUserPaymentItems = $this->getApplicationUserPaymentItemMapper($this->getDbSectionNameMain())->fetchAll($where);

        // アプリケーションユーザターゲット通貨ペイメントアイテム取得
        foreach ($applicationUserPaymentItems as $applicationUserPaymentItem) {
            $conditions[] = $applicationUserPaymentItem->getApplicationUserPaymentItemId();
        }
        $where2 = array('applicationUserPaymentItemId' => $conditions);
        return $this->getApplicationUserCurrencyPaymentItemMapper($this->getDbSectionNameMain())->fetchAll($where2);
    }

}
