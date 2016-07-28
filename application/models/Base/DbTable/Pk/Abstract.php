<?php

/**
 * Application_Model_Base_DbTable_Pk_Abstract
 * 
 * @category Zend
 * @package Application_Model_Base_DbTable
 * @subpackage Pk
 */

/**
 * @category Zend
 * @package Application_Model_Base_DbTable
 * @subpackage Pk
 */
abstract class Application_Model_Base_DbTable_Pk_Abstract extends Application_Model_DbTable_Abstract
{
    /**
     * TableDataを更新する。
     *
     * @param mixed $data 更新情報の配列
     * @param mixed $pk プライマリキーの連想配列
     * @return int The number of rows updated.
     */
    public function updateByPk()
    {
        $args = func_get_args();
        $data = array_shift($args);
        
        return $this->update($data, call_user_func_array(array($this, '_createPkWhereClause'), $args));
    }

    /**
     * TableDataを削除する。
     *
     * @param mixed $pk プライマリキーの連想配列
     * @return int The number of rows deleted.
     */
    public function deleteByPk()
    {
        return $this->delete(call_user_func_array(array($this, '_createPkWhereClause'), func_get_args()));
    }
    
    /**
     * where句の文字列を返す
     *
     * @param mixed $pk プライマリキーの連想配列
     * @return string where句の文字列
     */
    private function _createPkWhereClause()
    {
        $this->_setupPrimaryKey();
        $args = func_get_args();
        $keyNames = array_values((array) $this->_primary);
        
        $whereList = array();
        $numberTerms = 0;
        foreach ($args as $keyPosition => $keyValues) {
            $keyValuesCount = count($keyValues);
            // Coerce the values to an array.
            // Don't simply typecast to array, because the values
            // might be Zend_Db_Expr objects.
            if (!is_array($keyValues)) {
                $keyValues = array($keyValues);
            }
            if ($numberTerms == 0) {
                $numberTerms = $keyValuesCount;
            } else if ($keyValuesCount != $numberTerms) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Missing value(s) for the primary key");
            }
            $keyValues = array_values($keyValues);
            for ($i = 0; $i < $keyValuesCount; ++$i) {
                if (!isset($whereList[$i])) {
                    $whereList[$i] = array();
                }
                $whereList[$i][$keyPosition] = $keyValues[$i];
            }
        }
        
        $whereClause = null;
        if (count($whereList)) {
            $whereOrTerms = array();
            $tableName = $this->_db->quoteTableAs($this->_name, null, true);
            foreach ($whereList as $keyValueSets) {
                $whereAndTerms = array();
                foreach ($keyValueSets as $keyPosition => $keyValue) {
                    $type = $this->_metadata[$keyNames[$keyPosition]]['DATA_TYPE'];
                    $columnName = $this->_db->quoteIdentifier($keyNames[$keyPosition], true);
                    $whereAndTerms[] = $this->_db->quoteInto(
                        $tableName . '.' . $columnName . ' = ?',
                        $keyValue, $type);
                }
                $whereOrTerms[] = '(' . implode(' AND ', $whereAndTerms) . ')';
            }
            $whereClause = '(' . implode(' OR ', $whereOrTerms) . ')';
        }
                
        return $whereClause;
    }

    /**
     * レコードの件数を返す。
     *
     * @param array $where SELECTの条件を指定する連想配列
     * @return int The number of rows.
     */
    public function count($where)
    {
        if (!($where instanceof Zend_Db_Table_Select)) {
            $select = $this->select();
        
            if ($where !== null) {
                $this->_where($select, $where);
            }
        
        } else {
            $select = $where;
        }
        
        $select->from($this, array('count(*) as amount'));
        $rows = $this->fetchAll($select);
        
        return($rows[0]->amount);
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
            throw  new Common_Exception_NotSupported('not supported');
        } 
        
        // get the columns for the table
        $tableInfo = $this->info();
        $tableColumns = $tableInfo['cols'];
        
        // columns submitted for insert
        $dataColumns = array_keys($data);
        
        // intersection of table and insert cols
        $valueColumns = array_intersect($tableColumns, $dataColumns);
        sort($valueColumns);
        
        // generate SQL statement
        $cols = '';
        $vals = '';
        foreach($valueColumns as $col) {
            $cols .= $this->getAdapter()->quoteIdentifier($col) . ',';
            $vals .= $this->getAdapter()->quoteInto('?', $data[$col]);
            $vals .= ',';
        }
        $cols = rtrim($cols, ',');
        $vals = rtrim($vals, ',');
        $sql = 'REPLACE INTO ' . $this->_name . ' (' . $cols . ') VALUES (' . $vals . ');';
        
        return $this->_db->query($sql);
    }
}
