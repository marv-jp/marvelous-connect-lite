<?php

namespace misp\logics\payment\traits;

/**
 * アプリケーションユーザペイメントアイテムのトレイトです。
 * 
 * ※Logic_Payment_Abstract を継承しているサブクラスでの use を前提としています。
 */
trait Logic_Payment_Trait_ApplicationUserPaymentItemRepository
{

    /**
     * アプリケーションユーザペイメントアイテムレコードを検索します。
     * 
     * @param int $applicationUserPaymentItemId
     * @param string $dbSectionName
     * @return Application_Model_ApplicationUserPaymentItem
     */
    public function findApplicationUserPaymentItem($applicationUserPaymentItemId, $dbSectionName = NULL)
    {
        if (is_null($dbSectionName)) {
            $dbSectionName = $this->getDbSectionNameSub();
        }

        $applicationUserPaymentItemMapper = $this->getApplicationUserPaymentItemMapper($dbSectionName);
        $applicationUserPaymentItem       = $applicationUserPaymentItemMapper->find($applicationUserPaymentItemId);

        return $applicationUserPaymentItem;
    }

    /**
     * アプリケーションユーザペイメントアイテムを登録します。
     * 
     * @param int $applicationUserPaymentId
     * @return int LastInsertId
     */
    public function saveApplicationUserPaymentItem($applicationUserPaymentId)
    {
        $applicationUserPaymentItemMapper = $this->getApplicationUserPaymentItemMapper($this->getDbSectionNameMain());
        $applicationUserPaymentItem       = new \Application_Model_ApplicationUserPaymentItem();
        $applicationUserPaymentItem->setApplicationUserPaymentId($applicationUserPaymentId);
        $applicationUserPaymentItem->setCreatedDate($this->_nowDatetime);
        $applicationUserPaymentItem->setUpdatedDate($this->_nowDatetime);
        return $applicationUserPaymentItemMapper->insert($applicationUserPaymentItem);
    }

    /**
     * アプリケーションユーザペイメントアイテムを取得します。
     * 
     * @param Application_Model_ApplicationUserPaymentItem $conditionModel
     * @param string $dbSectionName
     * @return array Application_Model_ApplicationUserPaymentItem
     */
    public function fetchAllApplicationUserPaymentItem($conditionModel, $dbSectionName = NULL)
    {
        if (is_null($dbSectionName)) {
            $dbSectionName = $this->getDbSectionNameSub();
        }

        $where   = [];
        $wkWhere = $conditionModel->toArray();
        foreach ($wkWhere as $key => $value) {
            if (\Common_Util_String::isNotEmpty($value)) {
                $where[$key] = [$value];
            }
        }

        return $this->getApplicationUserPaymentItemMapper($dbSectionName)->fetchAll($where);
    }

}
