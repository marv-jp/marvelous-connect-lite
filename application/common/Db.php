<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Db
 *
 * @author oguray
 */
class Common_Db extends Zend_Db
{
    /** @var string デフォルト接続名 */
    const MAIN_DB = 'mainDb';

    /** @var array 接続別のトランザクションの状態 */
    protected static $_transactionLevel = array();

    /**
     * DB接続を返す
     *
     * @param string $registName トランザクション対象のDB接続名
     * @return Zend_Db_Adapter_Abstract
     */
    public static function factoryByDbName($registName = self::MAIN_DB)
    {
        try
        {
            if (!Zend_Registry::isRegistered($registName))
            {
                $config = Zend_Registry::get('database_configs');

                // DB関連設定取得
                $adapterName = $config[$registName]['type'];
                unset($config[$registName]['type']);
                $db = parent::factory($adapterName, $config[$registName]);

                self::$_transactionLevel[$registName] = 0;

                Zend_Registry::set($registName, $db);
            }
            else
            {
                $db = Zend_Registry::get($registName);
            }

            // デフォルトDBの指定ならば、デフォルトDBアダプターとしてセットする
            if (self::MAIN_DB === $registName)
            {
                Zend_Db_Table::setDefaultAdapter($registName);
            }

            return $db;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /**
     * トランザクションを開始します。
     * 
     * @param mixed $registNames トランザクション対象のDB接続名 または DB接続名を格納した配列
     * @return mixed Zend_Db_Adapter_Abstract または DB接続名をキーにZend_Db_Adapter_Abstractを格納した連想配列
     */
    public static function beginTransaction($registNames = self::MAIN_DB)
    {
        if (is_array($registNames))
        {
            $dbList = array();
            foreach ($registNames as $registName)
            {
                $dbList[$registName] = self::_beginTransaction($registName);
            }
            
            return $dbList;
        }
        else 
        {
            return self::_beginTransaction($registNames);
        }
    }
    
    /**
     * トランザクションを開始します。
     * 
     * @param string $registName トランザクション対象のDB接続名
     * @return Zend_Db_Adapter_Abstract
     */
    private static function _beginTransaction($registName = self::MAIN_DB)
    {
        try
        {
            $db = Common_Db::factoryByDbName($registName);
            if (self::$_transactionLevel[$registName] === 0)
            {
                $db->beginTransaction();
            }
            self::$_transactionLevel[$registName]++;

            return $db;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /**
     * トランザクションをコミットします。
     *
     * @param mixed $registNames トランザクション対象のDB接続名 または DB接続名を格納した配列
     * @return mixed Zend_Db_Adapter_Abstract または DB接続名をキーにZend_Db_Adapter_Abstractを格納した連想配列
     */
    public static function commit($registNames = self::MAIN_DB)
    {
        if (is_array($registNames))
        {
            $dbList = array();
            foreach ($registNames as $registName)
            {
                $dbList[$registName] = self::_commit($registName);
            }
            
            return $dbList;
        }
        else 
        {
            return self::_commit($registNames);
        }
    }
    
    /**
     * トランザクションをコミットします。
     *
     * @param string $registName トランザクション対象のDB接続名
     * @return Zend_Db_Adapter_Abstract
     */
    private static function _commit($registName = self::MAIN_DB)
    {
        try
        {
            $db = Common_Db::factoryByDbName($registName);
            if (self::$_transactionLevel[$registName] === 1)
            {
                $db->commit();
            }
            self::$_transactionLevel[$registName]--;

            return $db;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /**
     * トランザクションをロールバックします。
     *
     * @param mixed $registNames トランザクション対象のDB接続名 または DB接続名を格納した配列
     * @return mixed Zend_Db_Adapter_Abstract または DB接続名をキーにZend_Db_Adapter_Abstractを格納した連想配列
     */
    public static function rollBack($registNames = self::MAIN_DB)
    {
        if (is_array($registNames))
        {
            $dbList = array();
            foreach ($registNames as $registName)
            {
                $dbList[$registName] = self::_rollBack($registName);
            }
            
            return $dbList;
        }
        else 
        {
            return self::_rollBack($registNames);
        }
    }
    
    /**
     * トランザクションをロールバックします。
     *
     * @param string $registName トランザクション対象のDB接続名
     * @return Zend_Db_Adapter_Abstract
     */
    private static function _rollBack($registName = self::MAIN_DB)
    {
        try
        {
            $db = Common_Db::factoryByDbName($registName);
            if (self::$_transactionLevel[$registName] === 1)
            {
                $db->rollBack();
            }
            self::$_transactionLevel[$registName]--;

            return $db;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

}
