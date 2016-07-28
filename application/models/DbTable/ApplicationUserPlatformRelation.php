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
class Application_Model_DbTable_ApplicationUserPlatformRelation extends Application_Model_Base_DbTable_ApplicationUserPlatformRelation
{

    /**
     * ID連携処理専用
     * 
     * @param array $where WHERE条件の連想配列
     *                           array('カラム名(camel case)' => 値,
     *                                  ...
     *                                 )
     * @return type
     */
    public function fetchAllCreateIdFederation($where)
    {
        // 取得カラムの設定
        $groupBy = array('platform_user_id', 'platform_id');

        // selectオブジェクト取得
        $select = $this->getAdapter()->select();

        // 対象テーブルを設定
        $select->from($this->_name, $groupBy);

        // 必要なカラムを設定        
        $select->group($groupBy);

        // 条件設定
        $whereWithPlaceholders = Common_Util_Db::keyNameCamelToSnakeWithPlaceholder($where);
        foreach ($whereWithPlaceholders as $whereWithPlaceholder => $value) {
            $select->where($whereWithPlaceholder, $value);
        }
        $select->where('deleted_date IS NULL');

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

