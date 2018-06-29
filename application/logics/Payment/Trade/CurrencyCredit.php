<?php

/**
 * Logic_Payment_Trade_CurrencyCreditクラスのファイル
 * 
 * Logic_Payment_Trade_CurrencyCreditクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */

/**
 * Logic_Payment_Trade_CurrencyCredit
 * 
 * 仮想通貨アクティビティ図：「通貨購入」
 * 
 * アプリケーションユーザの購入処理を行うクラス
 * 
 * @category Zend
 * @package  Logic_Payment_Trade
 */
class Logic_Payment_Trade_CurrencyCredit extends Logic_Payment_Trade_CurrencyAbstract
{

    /**
     * 実処理
     * 
     * この処理のトランザクションは呼び出し側に依存しています。
     * 
     * @throws Common_Exception_NotModified アプリケーションユーザ通貨レコードの更新結果が0件だった場合にThorwされます
     * @throws Common_Exception_IllegalParameter　パラメータチェックで不正が検出された場合にThrowされます
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    public function exec()
    {
        $applicationUserCurrencyCollection = $this->getApplicationUserCurrencyCollection();
        $applicationUserCurrencyCollection->rewind();

        // モデルがアタッチされていなければ処理続行しない
        if (!$applicationUserCurrencyCollection->count()) {
            throw new Common_Exception_IllegalParameter(sprintf('コレクションの中身がありません：[TargetCollection:%s]', get_class($applicationUserCurrencyCollection)));
        }

        // 「通貨購入：1」
        // (アタッチされた)モデル分、購入処理を行う
        $nowDatetime = $this->getNowDatetime();
        foreach ($applicationUserCurrencyCollection as $m) {

            $applicationUserId     = $m->getApplicationUserId();
            $applicationId         = $m->getApplicationId();
            $applicationWorldId    = $m->getApplicationWorldId();
            $applicationCurrencyId = $m->getApplicationCurrencyId();
            $paymentPlatformId     = $m->getPaymentPlatformId();
            $paymentDeviceId       = $m->getPaymentDeviceId();
            $paymentRatingId       = $m->getPaymentRatingId();
            $unitPrice             = $m->getUnitPrice();
            $currencyAmount        = $m->getCurrencyAmount();

            // パラメータチェック
            //   必須
            $this->_isValidateValue($applicationUserId);
            $this->_isValidateValue($applicationId);
            $this->_isValidateValue($applicationCurrencyId);
            $this->_isValidateValue($unitPrice);
            $this->_isValidateValue($currencyAmount);
            //   任意(長さのみチェック)
            $this->_isValidateLength($applicationWorldId);
            $this->_isValidateLength($paymentPlatformId, 191);
            $this->_isValidateLength($paymentDeviceId, 11);
            $this->_isValidateLength($paymentRatingId, 11);

            // Mapper取得
            $currencyMapper = $this->getApplicationUserCurrencyMapper($this->getDbSectionNameMain());

            // 1. 単価による分岐
            //   2.有償アプリケーションユーザ通貨情報の登録
            if ($m->getUnitPrice() != 0) {


                $applicationUserPaymentItemId = $m->getApplicationUserPaymentItemId();

                // 有償アプリケーションユーザ通貨情報の登録の場合のみ
                // アプリケーションユーザペイメントアイテムIDの必須チェックが必要
                $this->_isValidateValue($applicationUserPaymentItemId);

                $result = $currencyMapper->find($applicationUserPaymentItemId, $applicationCurrencyId, $paymentPlatformId, $paymentDeviceId, $paymentRatingId, $applicationUserId, $applicationId, $applicationWorldId, $unitPrice);

                if ($result) {
                    // 加算(Update)
                    $result->setCurrencyAmount($currencyAmount + $result->getCurrencyAmount());
                    $result->setUpdatedDate($nowDatetime);

                    if (!$currencyMapper->update($result, $applicationUserPaymentItemId, $applicationCurrencyId, $paymentPlatformId, $paymentDeviceId, $paymentRatingId, $applicationUserId, $applicationId, $applicationWorldId, $unitPrice)) {
                        throw new Common_Exception_NotModified(Logic_Payment_Const::LOG_MSG_UPDATE_FAIL . $this->_generateModelLogFormat($result));
                    }
                } else {
                    // 新規(Insert)
                    // 実行日時を YYYY-MM-dd HH:mm:ss 形式に変換
                    $m->setExecutedDate($this->formatExecutedDate($m->getExecutedDate()));

                    // 期限日時の計算
                    $expiredDate = $this->calcExpiredDatetime($m->getExecutedDate(), $applicationCurrencyId);

                    $m->setExpiredDate($expiredDate);
                    $m->setCreatedDate($nowDatetime);
                    $m->setUpdatedDate($nowDatetime);

                    if ($expiredDate && $expiredDate <= $nowDatetime) {

                        // 償却処理
                        $applicationUserCurrencyCancelLogMapper = $this->getApplicationUserCurrencyCancelLogMapper($this->getDbSectionNameMain());
                        $applicationUserCurrencyCancelLog       = new Application_Model_ApplicationUserCurrencyCancelLog($m->toArray());
                        if (!$applicationUserCurrencyCancelLogMapper->insert($applicationUserCurrencyCancelLog)) {
                            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($applicationUserCurrencyCancelLog));
                        }
                    } else {
                        if (!$currencyMapper->insert($m)) {
                            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
                        }
                    }
                }
            }
            // (elseをなくす意図)
            //   3.無償アプリケーションユーザ通貨情報取得
            if ($m->getUnitPrice() == 0) {

                $applicationUserPaymentItemId = 0; // 無償は0固定

                $result = $currencyMapper->find($applicationUserPaymentItemId, $applicationCurrencyId, $paymentPlatformId, $paymentDeviceId, $paymentRatingId, $applicationUserId, $applicationId, $applicationWorldId, $unitPrice);

                // 処理対象通貨レコードを取得できた場合は通貨額に加算(Update)する。
                // 取得できなかった場合は(その単価での)初購入なので、
                // モデルの情報でレコード登録する。
                if ($result) {
                    // 加算(Update)
                    $result->setCurrencyAmount($currencyAmount + $result->getCurrencyAmount());
                    $result->setUpdatedDate($nowDatetime);

                    if (!$currencyMapper->update($result, $applicationUserPaymentItemId, $applicationCurrencyId, $paymentPlatformId, $paymentDeviceId, $paymentRatingId, $applicationUserId, $applicationId, $applicationWorldId, $unitPrice)) {
                        throw new Common_Exception_NotModified(Logic_Payment_Const::LOG_MSG_UPDATE_FAIL . $this->_generateModelLogFormat($result));
                    }
                } else {
                    // 新規(Insert)
                    $m->setApplicationUserPaymentItemId($applicationUserPaymentItemId);
                    $m->setApplicationUserPaymentId(NULL);
                    $m->setExecutedDate(NULL);
                    $m->setExpiredDate(NULL);
                    $m->setCreatedDate($nowDatetime);
                    $m->setUpdatedDate($nowDatetime);

                    if (!$currencyMapper->insert($m)) {
                        throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
                    }
                }
            }
        }
    }

}
