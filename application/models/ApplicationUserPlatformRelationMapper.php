<?php

/**
 * 自動生成ファイル
 *
 * CreateMapperSubClassLogicで自動生成されたファイル
 *
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */

/**
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */
class Application_Model_ApplicationUserPlatformRelationMapper extends Application_Model_Base_ApplicationUserPlatformRelationMapper
{

    /**
     * WHERE条件以外のカラムを更新します。
     * 
     * 更新したいデータをモデルにセットしてください。
     * 値の入っていないカラムの更新は無視します。
     * 
     * @param Application_Model_ApplicationUserPlatformRelation $applicationUserPlatformRelation
     * @return int 更新行数
     */
    public function updateByApplicationUserIdAndApplicationId(Application_Model_ApplicationUserPlatformRelation $applicationUserPlatformRelation)
    {
        $db = $this->getDbTable();

        // 条件
        $applicationUserId = $applicationUserPlatformRelation->getApplicationUserId();
        $applicationId     = $applicationUserPlatformRelation->getApplicationId();
        $where             = array('application_user_id = ?' => $applicationUserId, 'application_id = ?'      => $applicationId);


        // 更新データの調整
        // 1. 更新対象外(=値が空)の項目を除去
        // 2. Whereキーを除去
        $tmp        = $this->_dataSet($applicationUserPlatformRelation);
        $updateData = $tmp;
        foreach ($tmp as $column => $value) {
            if (strlen($value)) {
                continue;
            }
            unset($updateData[$column]);
        }
        unset($updateData['application_user_id']);
        unset($updateData['application_id']);

        $result = $db->update($updateData, $where);

        return $result;
    }

    /**
     * WHERE条件以外のカラムを更新します。
     * 
     * 更新したいデータをモデルにセットしてください。
     * 値の入っていないカラムの更新は無視します。
     * 
     * @param Application_Model_ApplicationUserPlatformRelation $applicationUserPlatformRelation
     * @return int 更新行数
     */
    public function updateByPlatformUserIdAndPlatformId(Application_Model_ApplicationUserPlatformRelation $applicationUserPlatformRelation)
    {
        $db = $this->getDbTable();

        // 条件
        $platformUserId = $applicationUserPlatformRelation->getPlatformUserId();
        $platformId     = $applicationUserPlatformRelation->getPlatformId();
        $where          = array('platform_user_id = ?' => $platformUserId, 'platform_id = ?'      => $platformId);


        // 更新データの調整
        // 1. 更新対象外(=値が空)の項目を除去
        // 2. Whereキーを除去
        $tmp        = $this->_dataSet($applicationUserPlatformRelation);
        $updateData = $tmp;
        foreach ($tmp as $column => $value) {
            if (strlen($value)) {
                continue;
            }
            unset($updateData[$column]);
        }
        unset($updateData['platform_user_id']);
        unset($updateData['platform_id']);

        $result = $db->update($updateData, $where);

        return $result;
    }

    /**
     * WHERE条件以外のカラムを更新します。
     * 
     * 更新したいデータをモデルにセットしてください。
     * 値の入っていないカラムの更新は無視します。
     * 
     * @param Application_Model_ApplicationUserPlatformRelation $applicationUserPlatformRelation
     * @return int 更新行数
     */
    public function updateByPlatformUserIdAndPlatformIdAndApplicationId(Application_Model_ApplicationUserPlatformRelation $applicationUserPlatformRelation)
    {
        $db = $this->getDbTable();

        // 条件
        $platformUserId = $applicationUserPlatformRelation->getPlatformUserId();
        $platformId     = $applicationUserPlatformRelation->getPlatformId();
        $applicationId  = $applicationUserPlatformRelation->getApplicationId();
        $where          = array('platform_user_id = ?' => $platformUserId, 'platform_id = ?'      => $platformId, 'application_id = ?'   => $applicationId);


        // 更新データの調整
        // 1. 更新対象外(=値が空)の項目を除去
        // 2. Whereキーを除去
        $tmp        = $this->_dataSet($applicationUserPlatformRelation);
        $updateData = $tmp;
        foreach ($tmp as $column => $value) {
            if (strlen($value)) {
                continue;
            }
            unset($updateData[$column]);
        }
        unset($updateData['platform_user_id']);
        unset($updateData['platform_id']);
        unset($updateData['application_id']);

        $result = $db->update($updateData, $where);

        return $result;
    }

    public function fetchAllCreateIdFederation($where)
    {
        $db = $this->getDbTable();

        $results = $db->fetchAllCreateIdFederation($where);

        $return = array();
        foreach ($results as $result) {
            $return[] = new Application_Model_ApplicationUserPlatformRelation($result);
        }

        return $return;
    }

}

