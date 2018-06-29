<?php

/**
 * Logic_Payment_BalanceHistoryクラスのファイル
 *
 * Logic_Payment_BalanceHistoryクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_BalanceHistory
 *
 * 所持通貨・履歴取り扱いクラス
 * 
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Payment_BalanceHistory extends Logic_Payment_Abstract
{

    private $_totalResults = 0;

    /**
     * 所持通貨を返します。
     * 
     * @param Application_Model_ApplicationUserCurrency $conditionalApplicationUserCurrency
     * @return array 
     */
    public function getBalance($conditionalApplicationUserCurrency)
    {
        $mapper = $this->getApplicationUserCurrencyMapper($this->getDbSectionNameSub());
        $where  = [
            'applicationId'       => [$conditionalApplicationUserCurrency->getApplicationId()],
            'applicationUserId'   => [$conditionalApplicationUserCurrency->getApplicationUserId()],
            'applicationWorldId'  => [$conditionalApplicationUserCurrency->getApplicationWorldId()],
            'paymentPlatformId'   => [$conditionalApplicationUserCurrency->getPaymentPlatformId()],
            'paymentDeviceId'     => [$conditionalApplicationUserCurrency->getPaymentDeviceId()],
            'paymentRatingId'     => [$conditionalApplicationUserCurrency->getPaymentRatingId()],
            'deletedDate IS NULL' => NULL,
        ];

        return $mapper->fetchAll($where);
    }

    /**
     * 履歴を返します。
     * 
     * @param Application_Model_ApplicationUserPayment $conditionalApplicationUserPayment
     * @return array 
     */
    public function getHistory($conditionalApplicationUserPayment, $type, $count, $startIndex)
    {
        // 条件
        $where = [
            'applicationId'      => $conditionalApplicationUserPayment->getApplicationId(),
            'applicationUserId'  => $conditionalApplicationUserPayment->getApplicationUserId(),
            'applicationWorldId' => $conditionalApplicationUserPayment->getApplicationWorldId(),
        ];

        $offset        = $startIndex - 1;
        $dbSectionName = $this->getDbSectionNameSub();

        $applicationUserPaymentHistories = '';

        // $typeによる分岐
        switch ($type) {
            case Logic_Payment_Const::HISTORY_TYPE_CREDIT:
                // 購入履歴
                $order               = [
                    'executedDate'             => 'DESC',
                    'applicationUserPaymentId' => 'DESC'
                ];
                $mapper              = $this->getApplicationUserCurrencyCreditLogMapper($dbSectionName);
                $models              = $mapper->fetchAll($where, $order, $count, $offset);
                $this->_totalResults = $mapper->count($where);

                $applicationUserPaymentHistories = $this->_mappingPaymentHistoryModelFromCreditLog($models);

                break;
            case Logic_Payment_Const::HISTORY_TYPE_CANCEL:

                // 償却履歴
                $order               = [
                    'createdDate'              => 'DESC',
                    'applicationUserPaymentId' => 'DESC'
                ];
                $mapper              = $this->getApplicationUserCurrencyCancelLogMapper($dbSectionName);
                $models              = $mapper->fetchAll($where, $order, $count, $offset);
                $this->_totalResults = $mapper->count($where);

                $applicationUserPaymentHistories = $this->_mappingPaymentHistoryModelFromCancelLog($models);

                break;

            default :
                // 購入・償却履歴
                $order                           = [
                    'published_date DESC',
                    'application_user_payment_id DESC'
                ];
                $mapper                          = $this->getApplicationUserPaymentHistoryMapper($dbSectionName);
                $applicationUserPaymentHistories = $mapper->fetchAll($where, $order, $count, $offset);

                $this->_totalResults = $mapper->count($where);
                break;
        }

        return $applicationUserPaymentHistories;
    }

    /**
     * totalResultsを返します。
     * 
     * @return int 
     */
    public function getHistoryTotalResults()
    {
        return $this->_totalResults;
    }

    /**
     * 通貨購入ログをペイメント履歴モデルに詰め替え
     * 
     * @param array $applicationUserCurrencyCreditLogs
     * @return array
     */
    private function _mappingPaymentHistoryModelFromCreditLog($applicationUserCurrencyCreditLogs)
    {
        $responseArray = [];

        foreach ($applicationUserCurrencyCreditLogs as $applicationUserCurrencyCreditLog) {
            $responseModel   = new Application_Model_ApplicationUserPaymentHistory($applicationUserCurrencyCreditLog->toArray());
            $responseModel->setType(Logic_Payment_Const::HISTORY_TYPE_CREDIT);
            $unitPrice       = (string) ($applicationUserCurrencyCreditLog->getPrice() / $applicationUserCurrencyCreditLog->getCurrencyAmount());
            $unitPrice       = sprintf('%.4f', $unitPrice);
            $responseModel->setUnitPrice($unitPrice);
            $responseModel->setPublishedDate($applicationUserCurrencyCreditLog->getExecutedDate());
            $responseArray[] = $responseModel;
        }

        return $responseArray;
    }

    /**
     * 通貨取消ログをペイメント履歴モデルに詰め替え
     * 
     * @param array $applicationUserCurrencyCancelLogs
     * @return array
     */
    private function _mappingPaymentHistoryModelFromCancelLog($applicationUserCurrencyCancelLogs)
    {
        $responseArray = [];

        foreach ($applicationUserCurrencyCancelLogs as $applicationUserCurrencyCancelLog) {
            $responseModel   = new Application_Model_ApplicationUserPaymentHistory($applicationUserCurrencyCancelLog->toArray());
            $responseModel->setType(Logic_Payment_Const::HISTORY_TYPE_CANCEL);
            $responseModel->setPublishedDate($applicationUserCurrencyCancelLog->getCreatedDate());
            $responseArray[] = $responseModel;
        }

        return $responseArray;
    }

}
