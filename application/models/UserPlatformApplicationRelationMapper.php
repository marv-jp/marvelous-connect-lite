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
class Application_Model_UserPlatformApplicationRelationMapper extends Application_Model_Base_UserPlatformApplicationRelationMapper
{

    /**
     * ユーザIDのレコードを論理削除します
     * 
     * 更新したいデータをモデルにセットしてください。
     * 値の入っていないカラムの更新は無視します。
     * 
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation
     * @return int 更新行数
     */
    public function deleteByUserId(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        $db = $this->getDbTable();

        // 条件
        $userId      = $userPlatformApplicationRelation->getUserId();
        $deletedDate = Common_Util_String::isEmpty($userPlatformApplicationRelation->getDeletedDate()) ? date('Y-m-d H:i:s') : $userPlatformApplicationRelation->getDeletedDate();
        $where       = array('user_id = ?' => $userId, 'deleted_date IS NULL' => NULL);

        // 更新データの調整
        $updateData = array('deleted_date' => $deletedDate);

        $result = $db->update($updateData, $where);

        return $result;
    }

    /**
     * WHERE条件以外のカラムを更新します。
     * 
     * 更新したいデータをモデルにセットしてください。
     * 値の入っていないカラムの更新は無視します。
     * 
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation
     * @return int 更新行数
     */
    public function updateByUserIdAndApplicationId(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        $db = $this->getDbTable();

        // 条件
        $userId        = $userPlatformApplicationRelation->getUserId();
        $applicationId = $userPlatformApplicationRelation->getApplicationId();
        $where         = array('user_id = ?' => $userId, 'application_id = ?' => $applicationId);


        // 更新データの調整
        // 1. 更新対象外(=値が空)の項目を除去
        // 2. Whereキーを除去
        $tmp        = $this->_dataSet($userPlatformApplicationRelation);
        $updateData = $tmp;
        foreach ($tmp as $column => $value) {
            if (strlen($value)) {
                continue;
            }
            unset($updateData[$column]);
        }
        unset($updateData['user_id']);
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
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation
     * @return int 更新行数
     */
    public function updateByPlatformUserIdAndPlatformId(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        $db = $this->getDbTable();

        // 条件
        $platformUserId = $userPlatformApplicationRelation->getPlatformUserId();
        $platformId     = $userPlatformApplicationRelation->getPlatformId();
        $where          = array('platform_user_id = ?' => $platformUserId, 'platform_id = ?' => $platformId);


        // 更新データの調整
        // 1. 更新対象外(=値が空)の項目を除去
        // 2. Whereキーを除去
        $tmp        = $this->_dataSet($userPlatformApplicationRelation);
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
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation
     * @return int 更新行数
     */
    public function updateUserIdAndPlatformIdAndApplicationId(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        $db = $this->getDbTable();

        // 条件
        $userId        = $userPlatformApplicationRelation->getUserId();
        $platformId    = $userPlatformApplicationRelation->getPlatformId();
        $applicationId = $userPlatformApplicationRelation->getApplicationId();
        $where         = array('user_id = ?' => $userId, 'platform_id = ?' => $platformId, 'application_id = ?' => $applicationId);


        // 更新データの調整
        // 1. 更新対象外(=値が空)の項目を除去
        // 2. Whereキーを除去
        $tmp        = $this->_dataSet($userPlatformApplicationRelation);
        $updateData = $tmp;
        foreach ($tmp as $column => $value) {
            if (strlen($value)) {
                continue;
            }
            unset($updateData[$column]);
        }
        unset($updateData['user_id']);
        unset($updateData['platform_id']);
        unset($updateData['application_id']);

        $result = $db->update($updateData, $where);

        return $result;
    }

    public function fetchAllCreateIdFederation($where, $applicationId)
    {
        $db = $this->getDbTable();

        $results = $db->fetchAllCreateIdFederation($where, $applicationId);

        $return = array();
        foreach ($results as $result) {
            $return[] = new Application_Model_UserPlatformApplicationRelation($result);
        }

        return $return;
    }

    /**
     * ID連携状態確認処理専用
     * 
     * @param array $where
     * @param type $groupBy
     * @return Application_Model_UserPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデルの配列
     *          (IDE補完の便宜のために型情報はオブジェクトにしています)
     */
    public function fetchAllReadIdFederationStatus(array $where, $groupBy)
    {
        $db = $this->getDbTable();

        $results = $db->fetchAllReadIdFederationStatus($where, $groupBy);

        $return = array();
        foreach ($results as $result) {
            $return[] = new Application_Model_UserPlatformApplicationRelation($result);
        }

        return $return;
    }

}
