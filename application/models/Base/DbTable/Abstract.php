<?php

/**
 * Application_Model_Base_DbTable_Abstract
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage DbTable
 */


/**
 * Application_Model_Base_DbTable_Abstractクラス
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage DbTable
 */
abstract class Application_Model_Base_DbTable_Abstract extends Zend_Db_Table_Abstract 
{
    protected $_name = '';
    
    /**
     * checksumメソッド
     *
     * @return int チェックサム
     */
    public function checksum()
    {
        // ドライバーの指定を確認
        if (!($this->_db instanceof Zend_Db_Adapter_Pdo_Mysql)) {
            throw  new Common_Exception_NotSupported('not supported');
        } 
        
        $sql = 'checksum table ' . $this->_name . ';';
        
        $statement = $this->_db->query($sql);
        $result = $statement->fetch();
        
        return $result['Checksum'];
    }
    
    /**
     * テーブル名を返す
     *
     * @return string テーブル名
     */
    public function getName()
    {
        return $this->_name;
    }
}

