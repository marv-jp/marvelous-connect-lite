<?php

/**
 * Logic_Payment_Trade_Paymentクラスのファイル
 *
 * Logic_Payment_Trade_Paymentクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */

/**
 * Logic_Payment_Trade_Payment
 *
 * 仮想通貨取引：消費
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */
class Logic_Payment_Trade_Payment extends Logic_Payment_Trade_Abstract
{

    /**
     * 実処理
     * 
     * この処理のトランザクションは呼び出し側に依存しています。
     * 
     * @throws Common_Exception_NotFound レコードが見つからなかった場合にThrowされます
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    public function exec()
    {
        // パラメータチェック
        // paidWithのチェック
        $paidWith = $this->pickUpPaidWith();
        if ($paidWith) {
            if ($this->isNotAllowedPaidWith($this->pickUpPaidWith())) {
                throw new Common_Exception_IllegalParameter(Logic_Const::LOG_MSG_ILLEGAL_PARAMETER . 'paidWith = ' . $paidWith);
            }
        }

        $applicationUserPayment = $this->getApplicationUserPayment();
        $dbSectionName          = $this->getDbSectionNameMain();

        // Mapper
        $applicationUserTargetProductPaymentItemMapper = $this->getApplicationUserTargetProductPaymentItemMapper($dbSectionName);

        // アプリケーションユーザ通貨ペイメントアイテム取得(from)
        $applicationUserCurrencyPaymentItems = $this->_readFromApplicationUserCurrencyPaymentItem();

        // 残高不足チェック
        $this->_isCurrencyEnoughForUpdate($applicationUserCurrencyPaymentItems);

        // 「通貨消費」ロジック取得
        $currencyPaymentLogic = $this->getCurrencyPaymentLogic();
        $currencyPaymentLogic->setPaidWith($paidWith);
        
        foreach ($applicationUserCurrencyPaymentItems as $applicationUserCurrencyPaymentItem) {

            // 通貨消費
            // 初期化処理
            $currencyPaymentLogic->setApplicationUserPayment($applicationUserPayment);
            $currencyPaymentLogic->setApplicationUserCurrencyPaymentItem($applicationUserCurrencyPaymentItem);
            // 3.通貨消費呼び出し
            $applicationUserCurrencyCollection = $currencyPaymentLogic->exec();
            $applicationUserCurrencyCollection->rewind();

            $where                                    = array('applicationUserPaymentItemId' => array($applicationUserCurrencyPaymentItem->getApplicationUserPaymentItemId()));
            $applicationUserTargetProductPaymentItems = $applicationUserTargetProductPaymentItemMapper->fetchAll($where);
            if (!$applicationUserTargetProductPaymentItems) {
                throw new Common_Exception_NotFound(sprintf(Logic_Payment_Const::LOG_MSG_RECORD_NOT_FOUND . '[ApplicationUserPaymentItemId:%s]', $where['applicationUserPaymentItemId']));
            }

            // 消費ログ出力
            $this->_createApplicationUserCurrencyPaymentLog($applicationUserCurrencyCollection, $applicationUserCurrencyPaymentItem, $applicationUserTargetProductPaymentItems);
        }
    }

    /**
     * アプリケーションユーザ通貨ペイメントログ登録
     * 
     * この処理のトランザクションは呼び出し側に依存しています。
     * 
     * @param Misp_Collection_ApplicationUserCurrency $applicationUserCurrencyCollection
     * @param Application_Model_ApplicationUserCurrencyPaymentItem $applicationUserCurrencyPaymentItem
     * @param Application_Model_ApplicationUserTargetProductPaymentItem[] $applicationUserTargetProductPaymentItems
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    private function _createApplicationUserCurrencyPaymentLog(Misp_Collection_ApplicationUserCurrency $applicationUserCurrencyCollection, Application_Model_ApplicationUserCurrencyPaymentItem $applicationUserCurrencyPaymentItem, array $applicationUserTargetProductPaymentItems)
    {
        $nowDatetime               = $this->getNowDatetime();

        foreach ($applicationUserCurrencyCollection as $applicationUserCurrency) {

            $logModel = new Application_Model_ApplicationUserCurrencyPaymentLog($applicationUserCurrency->toArray());
            $logModel->setApplicationUserPaymentItemId($applicationUserCurrencyPaymentItem->getApplicationUserPaymentItemId());
            $logModel->setApplicationUserPaymentId($this->getApplicationUserPayment()->getApplicationUserPaymentId());
            $logModel->setPaymentPlatformId($this->getApplicationUserPayment()->getPaymentPlatformId());
            $logModel->setPaymentDeviceId($this->getApplicationUserPayment()->getPaymentDeviceId());
            $logModel->setPaymentRatingId($this->getApplicationUserPayment()->getPaymentRatingId());
            $logModel->setCreatedDate($nowDatetime);
            $logModel->setUpdatedDate($nowDatetime);

            foreach ($applicationUserTargetProductPaymentItems as $applicationUserTargetProductPaymentItem) {
                $logModel->setApplicationProductId($applicationUserTargetProductPaymentItem->getApplicationProductId());
                $logModel->setProductQuantity($applicationUserTargetProductPaymentItem->getProductQuantity());

                // 消費ログモデルをテキストログ配列にプッシュ
                Misp_TextLog::getInstance()->push($logModel);
            }
        }
    }

}
