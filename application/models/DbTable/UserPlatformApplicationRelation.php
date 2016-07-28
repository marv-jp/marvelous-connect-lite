<?php

/**
 * 自動生成ファイル
 *
 * CreateDataMapperSubClassLogicで自動生成されたファイル
 *
 * @category Zend
 * @package Application_Model
 * @subpackage DbTable
 */

/**
 * @category Zend
 * @package Application_Model
 * @subpackage DbTable
 */
class Application_Model_DbTable_UserPlatformApplicationRelation extends Application_Model_Base_DbTable_UserPlatformApplicationRelation
{

    /**
     * ID連携状態確認処理専用
     * 
     * @param array $where WHERE条件の連想配列
     *                           array('カラム名(camel case)' => 値,
     *                                  ...
     *                                 )
     * @param mixed $groupBy Group By句のカラム名(camel case)
     * @return type
     */
    public function fetchAllReadIdFederationStatus(array $where, $groupBy)
    {
        // 取得カラムの設定
        $cols    = array();
        $groupBy = (array) $groupBy;
        foreach ($groupBy as $col) {
            $cols[] = Common_Util_String::camelToSnake($col);
        }

        // selectオブジェクト取得
        $select = $this->getAdapter()->select();

        // DML構築
        $select->from($this->getName(), $cols);

        // 条件設定
        $whereWithPlaceholders = Common_Util_Db::keyNameCamelToSnakeWithPlaceholder($where);
        foreach ($whereWithPlaceholders as $whereWithPlaceholder => $value) {
            $select->where($whereWithPlaceholder, $value);
        }
        $select->where('deleted_date IS NULL');

        $select->group($cols);

        // 実行
        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;
    }

    /**
     * ID連携処理専用
     * 
     * @param array $where プラットフォームユーザ情報のWHERE条件の連想配列(複数指定した場合、OR条件となる)
     *                        array(
     *                           array('platformUserId' => 値,
     *                                 'platformId' => 値,
     *                                 ),
     *                           ...
     *                             )
     * @param string $applicationId Where条件として使用するアプリケーションID、すべてのOR条件に含まれる
     * @return type
     */
    public function fetchAllCreateIdFederation($where, $applicationId)
    {
        // 取得カラムの設定
        $groupBy = array('user_id');

        // selectオブジェクト取得
        $select = $this->getAdapter()->select();

        // 対象テーブルを設定
        $select->from($this->_name, $groupBy);

        // GroupByを設定
        $select->group($groupBy);

        // 条件設定
        foreach ($where as $wherePlatformUser) {
            $quotedPlatformUserId = $this->getAdapter()->quote($wherePlatformUser['platformUserId']);
            $quotedPlatformId     = $this->getAdapter()->quote($wherePlatformUser['platformId']);
            $quotedApplicationId  = $this->getAdapter()->quote($applicationId);

            $select->orWhere('platform_user_id = ' . $quotedPlatformUserId . ' AND platform_id = ' . $quotedPlatformId . ' AND application_id = ' . $quotedApplicationId . ' AND deleted_date IS NULL');
        }
        $select->order('user_id');


        // 実行
        $stmt   = $select->query();
        $result = $stmt->fetchAll();

        return $result;
    }

    /**
     * replaceメソッド
     *
     * @param array $data replace情報の連想配列
     * @return bool 実行結果
     */
    public function replace($data)
    {
        // ドライバーの指定を確認
        if (!($this->_db instanceof Zend_Db_Adapter_Pdo_Mysql)) {
            throw new Common_Exception_NotSupported('not supported');
        }

        // get the columns for the table
        $tableInfo    = $this->info();
        $tableColumns = $tableInfo['cols'];

        // columns submitted for insert
        $dataColumns = array_keys($data);

        // intersection of table and insert cols
        $valueColumns = array_intersect($tableColumns, $dataColumns);
        sort($valueColumns);

        // generate SQL statement
        $cols = '';
        $vals = '';
        foreach ($valueColumns as $col) {
            $cols .= $this->getAdapter()->quoteIdentifier($col) . ',';
            $vals .= is_null($data[$col]) ? 'NULL' : $this->getAdapter()->quoteInto('?', $data[$col]);
            $vals .= ',';
        }
        $cols = rtrim($cols, ',');
        $vals = rtrim($vals, ',');
        $sql  = 'REPLACE INTO ' . $this->_name . ' (' . $cols . ') VALUES (' . $vals . ');';

        return $this->_db->query($sql);
    }

}

