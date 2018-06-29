<?php

namespace misp\logics\payment\traits;

/**
 * プラットフォームペイメントのトレイトです。
 * 
 * ※Logic_Payment_Abstract を継承しているサブクラスでの use を前提としています。
 */
trait Logic_Payment_Trait_PlatformPaymentRepository
{

    /**
     * プラットフォームペイメントレコードを検索します。
     * 
     * @param int $platformPaymentId
     * @param string $paymentPlatformId
     * @param string $dbSectionName
     * @return \Application_Model_PlatformPayment
     */
    public function findPlatformPayment($platformPaymentId, $paymentPlatformId, $dbSectionName = NULL)
    {
        if (is_null($dbSectionName)) {
            $dbSectionName = $this->getDbSectionNameMain();
        }

        $platformPaymentMapper = $this->getPlatformPaymentMapper($dbSectionName);
        $platformPayment       = $platformPaymentMapper->find($platformPaymentId, $paymentPlatformId);

        return $platformPayment;
    }

    /**
     * プラットフォームペイメントテーブルを登録します。
     * 
     * @param Application_Model_PlatformPayment $m 登録情報をつめたモデル
     * @return int Last Insert Id
     * @throws Common_Exception_Exception
     */
    public function savePlatformPayment($m)
    {
        // Insert
        $mapper = $this->getPlatformPaymentMapper($this->getDbSectionNameMain());
        return $mapper->insert($m);
    }

    /**
     * プラットフォームペイメントテーブルを更新します。
     * 
     * @param Application_Model_PlatformPayment
     * @param int $platformPaymentId
     * @param string $paymentPlatformId
     */
    public function updatePlatformPayment($updateModel, $platformPaymentId, $paymentPlatformId)
    {
        $mapper = $this->getPlatformPaymentMapper($this->getDbSectionNameMain());
        return $mapper->update($updateModel, $platformPaymentId, $paymentPlatformId);
    }

}
