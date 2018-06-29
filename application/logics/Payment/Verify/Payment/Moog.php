<?php

/**
 * Logic_Payment_Verify_Payment_Moogクラスのファイル
 *
 * Logic_Payment_Verify_Payment_Moogクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment_Verify_Payment
 */

/**
 * Logic_Payment_Verify_Payment_Moog
 *
 * プラットフォーム決済情報検証：Moog
 *
 * @category Zend
 * @package  Logic_Payment_Verify_Payment
 */
class Logic_Payment_Verify_Payment_Moog extends Logic_Payment_Verify_Payment_Abstract
{

    public function verify()
    {
        // DB識別子を取得
        $dbSub  = $this->getDbSectionNameSub();
        $dbMain = $this->getDbSectionNameMain();

        // Mapper
        $applicationUserPaymentItemMapper               = $this->getApplicationUserPaymentItemMapper($dbSub);
        $applicationUserTargetCurrencyPaymentItemMapper = $this->getApplicationUserTargetCurrencyPaymentItemMapper($dbSub);
        $applicationUserPlatformPaymentRelationMapper   = $this->getApplicationUserPlatformPaymentRelationMapper($dbSub);
        $platformPaymentItemMapper                      = $this->getPlatformPaymentItemMapper($dbSub);
        $platformPaymentMapper                          = $this->getPlatformPaymentMapper($dbMain);

        $applicationUserPayment = $this->getApplicationUserPayment();

        // ORDER 時
        if ($applicationUserPayment->getPaymentStatus() == Logic_Payment_Const::PAYMENT_STATUS_ORDER) {

            // アプリケーションユーザペイメントアイテムの取得
            $result = $applicationUserPaymentItemMapper->fetchAll(array(
                'applicationUserPaymentId' => array($applicationUserPayment->getApplicationUserPaymentId()),
            ));
            if (!$result) {
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_FAILED_PAYMENT_NOT_FOUND);
                return FALSE;
            }
            $applicationUserPaymentItemId = $result[0]->getApplicationUserPaymentItemId();


            // アプリケーションユーザプラットフォームペイメント関連を取得
            $applicationUserPlatformPaymentRelations = $applicationUserPlatformPaymentRelationMapper->fetchAll(array(
                'applicationUserPaymentItemId' => array($applicationUserPaymentItemId)
            ));
            if (!$applicationUserPlatformPaymentRelations) {
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_FAILED_PAYMENT_NOT_FOUND);
                return FALSE;
            }
            $applicationUserPlatformPaymentRelation = $applicationUserPlatformPaymentRelations[0];


            // プラットフォームペイメントアイテムの取得
            $platformPaymentItems = $platformPaymentItemMapper->fetchAll(array(
                'platformPaymentItemId' => array($applicationUserPlatformPaymentRelation->getPlatformPaymentItemId())
            ));

            if (!$platformPaymentItems) {
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_FAILED_PAYMENT_NOT_FOUND);
                return FALSE;
            }

            $platformPaymentItem = $platformPaymentItems[0];
            $platformPaymentId   = $platformPaymentItem->getPlatformPaymentId();
            $paymentPlatformId   = $platformPaymentItem->getPaymentPlatformId();


            // プラットフォームペイメントの取得
            $platformPayment = $platformPaymentMapper->find($platformPaymentId, $paymentPlatformId);

            if (!$platformPayment) {
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_FAILED_PAYMENT_NOT_FOUND);
                return FALSE;
            }

            // プラットフォームペイメントの更新
            $platformPayment->setPlatformPaymentStatus($this->pickUpStatus());
            $platformPayment->setUpdatedDate($this->getNowDatetime());
            if (!$platformPaymentMapper->update($platformPayment, $platformPaymentId, $paymentPlatformId)) {
                throw new Common_Exception_NotModified(Logic_Payment_Const::LOG_MSG_UPDATE_FAIL . $this->_generateModelLogFormat($platformPayment));
            }

            foreach ($result as $applicationUserPaymentItem) {

                $applicationUserPaymentItemId = $applicationUserPaymentItem->getApplicationUserPaymentItemId();

                // ボーナスのチェック
                $bonusApplicationUserTargetCurrencyPaymentItems = $applicationUserTargetCurrencyPaymentItemMapper->fetchAll(array(
                    'applicationUserPaymentItemId' => array($applicationUserPaymentItemId),
                    'price'                        => array(Logic_Payment_Const::PRICE_BONUS)
                ));
                if ($bonusApplicationUserTargetCurrencyPaymentItems) {

                    $bonusApplicationUserTargetCurrencyPaymentItemCollection = new Misp_Collection_ApplicationUserTargetCurrencyPaymentItem();
                    foreach ($bonusApplicationUserTargetCurrencyPaymentItems as $bonusApplicationUserTargetCurrencyPaymentItem) {
                        $bonusApplicationUserTargetCurrencyPaymentItemCollection->attach($bonusApplicationUserTargetCurrencyPaymentItem);
                    }
                    $bonusApplicationUserTargetCurrencyPaymentItemCollection->rewind();
                }


                // 有償ログ出力のために下記を取得          
                //   アプリケーションユーザプラットフォームペイメント関連を取得
                $applicationUserPlatformPaymentRelations = $applicationUserPlatformPaymentRelationMapper->fetchAll(array(
                    'applicationUserPaymentItemId' => array($applicationUserPaymentItemId)
                ));
                if (!$applicationUserPlatformPaymentRelations) {
                    $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_FAILED_PAYMENT_NOT_FOUND);
                    return FALSE;
                }
                $applicationUserPlatformPaymentRelation = $applicationUserPlatformPaymentRelations[0];


                //   プラットフォームペイメントアイテムの取得
                $platformPaymentItems = $platformPaymentItemMapper->fetchAll(array(
                    'platformPaymentItemId' => array($applicationUserPlatformPaymentRelation->getPlatformPaymentItemId())
                ));
                if (!$platformPaymentItems) {
                    $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_FAILED_PAYMENT_NOT_FOUND);
                    return FALSE;
                }


                // アプリケーションユーザターゲット通貨ペイメントアイテムの取得
                $applicationUserTargetCurrencyPaymentItems = $applicationUserTargetCurrencyPaymentItemMapper->fetchAll(array(
                    'applicationUserPaymentItemId' => array($applicationUserPaymentItemId),
                    'price not'                    => array(Logic_Payment_Const::PRICE_BONUS)
                ));
                if (!$applicationUserTargetCurrencyPaymentItems) {
                    $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_FAILED_PAYMENT_NOT_FOUND);
                    return FALSE;
                }
                if ($bonusApplicationUserTargetCurrencyPaymentItems) {
                    $applicationUserTargetCurrencyPaymentItems[0]->setTargetCurrencyPaymentItemCollectionForBonus($bonusApplicationUserTargetCurrencyPaymentItemCollection);
                }
                foreach ($applicationUserTargetCurrencyPaymentItems as $applicationUserTargetCurrencyPaymentItem) {

                    $this->_targetCurrencyPaymentItemCollection->attach($applicationUserTargetCurrencyPaymentItem);

                    // 有償ログ出力
                    $platformPaymentItems[0]->setPrice($applicationUserTargetCurrencyPaymentItem->getPrice());
                    $this->_saveVerifiedApplicationUserCurrencyCreditLog($platformPaymentItems[0], $platformPayment, $applicationUserTargetCurrencyPaymentItem);
                }
            }

            $this->_targetCurrencyPaymentItemCollection->rewind();

            // 外部から取得できるようにセット
            $this->setPaymentItemCollection($this->_targetCurrencyPaymentItemCollection);

            //
        } else {
            // ペイメントステータスがorderでない場合、FALSEを返す
            $this->_logInfo(__CLASS__, __METHOD__, __LINE__, 'ペイメントステータスがorderではありません。');
            return FALSE;
        }

        return TRUE;
    }

    public function getPaymentIds(\Application_Model_CommonExternalPlatformPayment $externalPlatformPayment, Application_Model_CommonExternalPlatformPaymentItem $inRecieptPaymentItem = NULL)
    {
        return array();
    }

    /**
     * ペイメント種別とペイメントステータスの組み合わせが正しいことを検証します。
     * 
     * @param int $peymentType ペイメント種別
     * @param int $paymentStatus ペイメントステータス
     * @return boolean TRUE:正しい
     *                  FALSE:正しくない
     */
    static public function isValidPaymentTypePaymentStatusPair($peymentType, $paymentStatus)
    {
        switch ($paymentStatus) {

            case Logic_Payment_Const::PAYMENT_STATUS_START:

                switch ($peymentType) {

                    case Logic_Payment_Const::PAYMENT_TYPE_PAYMENT:
                    case Logic_Payment_Const::PAYMENT_TYPE_EXCHANGE:
                        return TRUE;
                }
        }
        return FALSE;
    }

}
