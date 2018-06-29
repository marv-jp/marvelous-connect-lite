<?php

namespace misp\logics\payment\traits;

/**
 * プラットフォームペイメントアイテムのトレイトです。
 * 
 * ※Logic_Payment_Abstract を継承しているサブクラスでの use を前提としています。
 */
trait Logic_Payment_Trait_PlatformPaymentItemRepository
{

    private $_traitPlatformPaymentItemModel;

    /**
     * プラットフォームペイメントアイテムを取得します。
     * 
     * @param Application_Model_ApplicationUserPayment $conditionModel
     * @param string $dbSectionName
     * @return array
     */
    public function fetchAllPlatformPaymentItem(\Application_Model_PlatformPaymentItem $conditionModel, $dbSectionName = NULL)
    {
        if (is_null($dbSectionName)) {
            $dbSectionName = $this->getDbSectionNameSub();
        }

        // プラットフォームペイメントアイテム取得
        $where   = [];
        $wkWhere = $conditionModel->toArray();
        foreach ($wkWhere as $key => $value) {
            if (\Common_Util_String::isNotEmpty($value)) {
                $where[$key] = [$value];
            }
        }

        return $this->getPlatformPaymentItemMapper($dbSectionName)->fetchAll($where);
    }

    /**
     * プラットフォームペイメントアイテムレコードを登録します。
     * 
     * @param mixed $data
     */
    public function savePlatformPaymentItem($data)
    {
        $platformPaymentItemMapper = $this->getPlatformPaymentItemMapper($this->getDbSectionNameMain());

        if ($data instanceof \Application_Model_PlatformPaymentItem) {
            // $dataがモデルの場合
            $platformPaymentItem = $data;
        } else {
            // $dataがモデルでなかった場合
            $platformPaymentItem = new \Application_Model_PlatformPaymentItem();
            $platformPaymentItem->setPlatformPaymentId($this->_paymentId);
            $platformPaymentItem->setPaymentPlatformId($this->_platform);
            $platformPaymentItem->setPlatformProductId($this->_platformProductId);
            $platformPaymentItem->setPrice($data['price']);
            $platformPaymentItem->setProductQuantity($data['quantity']);
            $platformPaymentItem->setExecutedDate($data['executedDate']);
            $platformPaymentItem->setCreatedDate($this->_nowDatetime);
            $platformPaymentItem->setUpdatedDate($this->_nowDatetime);

            $this->_traitPlatformPaymentItemModel = $platformPaymentItem;
        }

        // プラットフォームペイメントアイテムの登録
        return $platformPaymentItemMapper->insert($platformPaymentItem);
    }

    /**
     * プラットフォームペイメントアイテムレコードの登録に使用したモデルを返します。 
     * 
     * @return \Application_Model_PlatformPaymentItem
     */
    public function getTraitPlatformPaymentItem()
    {
        return $this->_traitPlatformPaymentItemModel;
    }

}
