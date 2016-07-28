<?php

/**
 * Application_Model_Base_DbTable_NoPk_Abstract
 * 
 * @category Zend
 * @package Application_Model_Base_DbTable
 * @subpackage NoPk
 */

/**
 * @category Zend
 * @package Application_Model_Base_DbTable
 * @subpackage NoPk
 */
abstract class Application_Model_Base_DbTable_NoPk_Abstract extends Application_Model_DbTable_Abstract 
{
    /**
     * Inserts a new row.
     *
     * @param array $data Column-value pairs.
     * @return null
     */
    public function insert($data)
    {
        $tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;
        $this->_db->insert($tableSpec, $data);
        return null;
    }

    /**
     * レコードの件数を返す。
     *
     * @param array $where SELECTの条件を指定する連想配列
     * @return int The number of rows.
     */
    public function count($where)
    {
        $select = $this->_db->select();
                
        if ($where instanceof Zend_Db_Select)
        {
            $select = $where;
        }
        else
        {
            $this->_where($select, $where);
        }
        
        $select->from($this->_name, 'count(*) as amount');
        
        $rows = $this->_fetch($select);
        
        return $rows[0]['amount'];
    }

    /**
     * Fetches all rows.
     *
     * @param string|array|Zend_Db_Select $where OPTIONAL An SQL WHERE clause or
     * Zend_Db_Table_Select object.
     * @param string|array $order OPTIONAL An SQL ORDER clause.
     * @param int $count OPTIONAL An SQL LIMIT count.
     * @param int $offset OPTIONAL An SQL LIMIT offset.
     * @return array rows.
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        if (!($where instanceof Zend_Db_Select)) 
        {
            $select = $this->_db->select()->from($this->_name);
        
            if ($where !== null) 
            {
                $this->_where($select, $where);
            }
        
            if ($order !== null) 
            {
                $this->_order($select, $order);
            }
        
            if ($count !== null || $offset !== null) 
            {
                $select->limit($count, $offset);
            }
        
        } 
        else 
        {
            $select = $where;
        }
        
        $rows = $this->_fetch($select);
        
        return $rows;
    }

    /**
     * Generate WHERE clause from user-supplied string or array
     *
     * @param Zend_Db_Select $select query options.
     * @param string|array $where OPTIONAL An SQL WHERE clause.
     * @return Zend_Db_Select
     */
    protected function _where($select, $where)
    {
        if (is_array($where))
        {            
            foreach ($where as $key => $val) 
            {
                $select->where($key, $val);
            }
        }
        else if(is_string($where))
        {
            $select->where($where);
        }
        
        return $select;
    }

    /**
     * Generate ORDER clause from user-supplied string or array
     *
     * @param Zend_Db_Select $select query options.
     * @param string|array $order OPTIONAL An SQL ORDER clause.
     * @return Zend_Db_Select
     */
    protected function _order($select, $order)
    {
        if (!is_array($order)) {
            $order = array($order);
        }
        
        foreach ($order as $val) {
            $select->order($val);
        }
        
        return $select;
    }

    /**
     * Support method for fetching rows.
     *
     * @param Zend_Db_Select $select query options.
     * @return array An array containing the row results in FETCH_ASSOC mode.
     */
    protected function _fetch($select)
    {
        $stmt = $this->_db->query($select);
        $data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        return $data;
    }
}
