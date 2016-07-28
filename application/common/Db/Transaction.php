<?php

class Common_Db_Transaction
{

    const TRANSACTION_MODE_TAG = 'tag';
    const TRANSACTION_MODE_NAME = 'name';
    
    protected static $_transactionMode = 'tag';

    /**
     * トランザクションを開始する。
     * 
     * @param mixed $names DB接続名 または DB接続名に設定したタグ
     * @return mixed Zend_Db_Adapter_Abstract または DB接続名をキーにZend_Db_Adapter_Abstractを格納した連想配列
     */
    public static function beginTransaction($names = array())
    {
        return Common_Db::beginTransaction(self::_resolveDbName($names));
    }

    /**
     * トランザクションをコミットする。
     *
     * @param mixed $names DB接続名 または DB接続名に設定したタグ
     * @return mixed Zend_Db_Adapter_Abstract または DB接続名をキーにZend_Db_Adapter_Abstractを格納した連想配列
     */
    public static function commit($names = array())
    {
        return Common_Db::commit(self::_resolveDbName($names));
    }
    
    /**
     * トランザクションをロールバックする。
     *
     * @param mixed $names DB接続名 または DB接続名に設定したタグ
     * @return mixed Zend_Db_Adapter_Abstract または DB接続名をキーにZend_Db_Adapter_Abstractを格納した連想配列
     */
    public static function rollBack($names = array())
    {
        return Common_Db::rollBack(self::_resolveDbName($names));
    }

    /**
     * トランザクションをDB接続名で扱うかタグで扱うかの切り替えを行う。
     * @param string $transactionMode
     */
    public static function setTransactionMode($transactionMode)
    {
        self::$_transactionMode = $transactionMode;
    }

    /**
     * タグ動作モード判定。
     * @return boolean タグ動作モードであれば正
     */
    public static function isTagMode()
    {
        return self::$_transactionMode == self::TRANSACTION_MODE_TAG;
    }
    
    /**
     * タグからDB接続名を取得する。
     * タグ動作モード出ない場合はそのまま返却する。
     * 
     * @param array|string $names
     * @return array DB接続名
     */
    protected static function _resolveDbName($names)
    {
        if (self::isTagMode()) {
            /** @var $dataSource Common_Db_DataSource */
            $dataSourceManager = Zend_Registry::get('dataSourceManager');
            return $dataSourceManager->getDbSectionsByTag($names);
        }
        return $names;
    }

    /**
     * ファンクションをトランザクション制御下で実行する。
     * 
     * @example
     * $dataSourceManager = Zend_Registry::get('dataSourceManager');            
     * $dbSection = $dataSourceManager->getDbSectionByTable('access_log');
     * $mapper = new Application_Model_AccessLogMapper($dbSection);
     * //$slaveMapper = new Application_Model_AccessLogMapper($dbSection . Common_Db_DataSource::CONSUMER_SERVER);
     *
     * Common_Db_Transaction::transaction(function () use ($mapper) {
     *     return $mapper->fetchAll();
     * }, array('log'));
     * 
     * @param Closure $function トランザクション中に実行する処理
     * @param array $transactionTags トランザクションの対象にするタグを格納した配列
     * @return mixed
     * @throws Exception
     */
    public static function transaction(Closure $function, $transactionTags)
    {
        try {
            Common_Db_Transaction::beginTransaction($transactionTags);
            $data = $function($this);
            Common_Db_Transaction::commit($transactionTags);
            return $data;
        } catch (Exception $ex) {
            Common_Db_Transaction::rollBack($transactionTags);
            throw $ex;
        }
    }
}
