<?php

/**
 * Application_Model_DbTable_Pk_Abstract
 * 
 * @category Zend
 * @package Application_Model_DbTable
 * @subpackage Pk
 */

/**
 * @category Zend
 * @package Application_Model_DbTable
 * @subpackage Pk
 */
abstract class Application_Model_DbTable_Pk_Abstract extends Application_Model_Base_DbTable_Pk_Abstract
{

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
