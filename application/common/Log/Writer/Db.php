<?php

/** Zend_Log_Writer_Abstract */
require_once 'Zend/Log/Writer/Abstract.php';

/**
 * Common_Log_Writer_Dbクラスのファイル
 * 
 * Common_Log_Writer_Dbクラスを定義している
 *
 * @category   Zend
 * @package    Common_Log
 * @subpackage Writer
 * @version    $Id$
 */

/**
 * Common_Log_Writer_Db
 * 
 * MySQL用の遅延書き込みWriterクラスです。
 * 
 * @category   Zend
 * @package    Common
 * @subpackage Writer
 */
class Common_Log_Writer_Db extends Zend_Log_Writer_Db
{
    /**
     * Database adapter instance
     *
     * @var Common_Db_Adapter_Pdo_Mysql
     */
    private $_db;

    /**
     * Name of the log table in the database
     *
     * @var string
     */
    private $_table;

    /**
     * Relates database columns names to log data field keys.
     *
     * @var null|array
     */
    private $_columnMap;

    /**
     * Class constructor
     *
     * @param Zend_Db_Adapter $db   Database adapter instance
     * @param string $table         Log table in database
     * @param array $columnMap
     * @return void
     */
    public function __construct($db, $table, $columnMap = null)
    {
        $this->_db    = $db;
        $this->_table = $table;
        $this->_columnMap = $columnMap;
    }

    /**
     * Remove reference to database adapter
     *
     * @return void
     */
    public function shutdown()
    {
        $this->_db = null;
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @return void
     * @throws Zend_Log_Exception
     */
    protected function _write($event)
    {
        if ($this->_db === null) {
            require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception('Database adapter is null');
        }

        if ($this->_columnMap === null) {
            $dataToInsert = $event;
        } else {
            $dataToInsert = array();
            foreach ($this->_columnMap as $columnName => $fieldKey) {
                $dataToInsert[$columnName] = $event[$fieldKey];
            }
        }

        $this->_db->insertDelayed($this->_table, $dataToInsert);
    }
}
