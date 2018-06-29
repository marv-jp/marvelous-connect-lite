<?php

namespace misp\logics\payment\traits;

/**
 * アプリケーションユーザプラットフォームペイメント関連のトレイトです。
 * 
 * ※Logic_Payment_Abstract を継承しているサブクラスでの use を前提としています。
 */
trait Logic_Payment_Trait_ApplicationUserPlatformPaymentRelationRepository
{

    /**
     * アプリケーションユーザプラットフォームペイメント関連を取得します。
     * 
     * @param Application_Model_ApplicationUserPlatformPaymentRelation $conditionModel
     * @param string $dbSectionName
     * @return array
     */
    public function fetchAllApplicationUserPlatformPaymentRelation(\Application_Model_ApplicationUserPlatformPaymentRelation $conditionModel, $dbSectionName = NULL)
    {
        if (is_null($dbSectionName)) {
            $dbSectionName = $this->getDbSectionNameSub();
        }

        // アプリケーションユーザプラットフォームペイメント関連取得
        $where   = [];
        $wkWhere = $conditionModel->toArray();
        foreach ($wkWhere as $key => $value) {
            if (\Common_Util_String::isNotEmpty($value)) {
                $where[$key] = [$value];
            }
        }

        return $this->getApplicationUserPlatformPaymentRelationMapper($dbSectionName)->fetchAll($where);
    }

    /**
     * アプリケーションユーザプラットフォームペイメント関連を登録します。
     * 
     * @param int $applicationUserPaymentItemId
     * @param int $platformPaymentItemId
     * @return int LastInsertId
     */
    public function saveApplicationUserPlatformPaymentRelation($applicationUserPaymentItemId, $platformPaymentItemId)
    {
        $applicationUserPlatformPaymentRelationMapper = $this->getApplicationUserPlatformPaymentRelationMapper($this->getDbSectionNameMain());
        $applicationUserPlatformPaymentRelation       = new \Application_Model_ApplicationUserPlatformPaymentRelation();
        $applicationUserPlatformPaymentRelation->setApplicationUserPaymentItemId($applicationUserPaymentItemId);
        $applicationUserPlatformPaymentRelation->setPlatformPaymentItemId($platformPaymentItemId);
        $applicationUserPlatformPaymentRelation->setCreatedDate($this->_nowDatetime);
        $applicationUserPlatformPaymentRelation->setUpdatedDate($this->_nowDatetime);

        return $applicationUserPlatformPaymentRelationMapper->insert($applicationUserPlatformPaymentRelation);
    }

    /**
     * アプリケーションユーザプラットフォームペイメント関連を検索します。
     * 
     * @param int $applicationUserPaymentItemId
     * @param string $dbSectionName
     * @return \Application_Model_ApplicationUserPlatformPaymentRelation
     */
    public function findApplicationUserPlatformPaymentRelation($applicationUserPaymentItemId, $dbSectionName = NULL)
    {
        if (is_null($dbSectionName)) {
            $dbSectionName = $this->getDbSectionNameSub();
        }

        $mapper = $this->getApplicationUserPlatformPaymentRelationMapper($dbSectionName);
        return $mapper->find($applicationUserPaymentItemId);
    }

}
