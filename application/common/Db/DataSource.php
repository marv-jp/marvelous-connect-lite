<?php

/**
 * DBアダプタ生成クラス。
 * cf) https://github.com/fedecarg/zf-replication-adapter
 */
class Common_Db_DataSource
{
    const SUPPLIER_SERVER     = 'master';
    const CONSUMER_SERVER     = 'slave';
    const ACTIVE_CONNECTION   = '%s_datasource_active_connection_%s';
    const FAILED_CONNECTIONS  = '%s_datasource_failed_connections_%s';
    
    /**
     * @var array
     */
    private $_config = array();
    
    /**
     * @var Zend_Cache_Core
     */
    private $_cache = null;
    
    /**
     * @var string
     */
    private $_cacheTag = '';
    
    /**
     * @var array
     */
    private $_adapters = array();
    
    /**
     * コンストラクタ。
     *
     * @param array|Zend_Config $config
     * @param Zend_Cache_Core $cache
     * @param string $cacheTag
     */
    public function __construct($config, Zend_Cache_Core $cache, $cacheTag)
    {
        $this->setConfig($config);
        $this->setCache($cache);
        $this->setCacheTag($cacheTag);
    }
    
    /**
     * 接続設定をセットする。
     *
     * @param array|Zend_Config $config
     * @return void
     */
    public function setConfig($config)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }
        $this->_config = $config;
    }
    
    /**
     * 接続設定を返す。
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }
    
    /**
     * キャッシュを設定する。
     *
     * @param Zend_Cache_Core $cache
     */
    public function setCache(Zend_Cache_Core $cache)
    {
        $this->_cache = $cache;
    }
    
    /**
     * キャッシュを返す。
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        return $this->_cache;
    }
    
    /**
     * キャッシュタグ名をセットする。
     *
     * @param string
     */
    public function setCacheTag($name)
    {
        $this->_cacheTag = $name;
    }
    
    /**
     * キャッシュタグ名を返す。
     *
     * @return string
     */
    public function getCacheTag()
    {
        return $this->_cacheTag;
    }
    
    /**
     * Zend_Db_Adapter_Abstractをセットする。
     * 
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param string $role Options: master, slave
     * @return void
     */
    public function setAdapter(Zend_Db_Adapter_Abstract $adapter, $role)
    {
        $namespace = sprintf(self::ACTIVE_CONNECTION, $this->getCacheTag(), strtolower($role));
        $this->_adapters[$namespace] = $adapter;
    }
    
    /**
     * Zend_Db_Adapter_Abstractを返す。
     * 設定されたサーバーリストからランダムに一つを返却する。
     * 
     * @param string $role master (supplier) or slave (consumer)
     * @return Zend_Db_Adapter_Abstract
     * @throws Zend_Db_Exception
     */
    public function getAdapter($role, $dbSection = null)
    {
        $role = strtolower($role);
        $namespace = sprintf(self::ACTIVE_CONNECTION, $this->getCacheTag(), $role);
        if ($this->hasAdapter($namespace)) {
            return $this->_adapters[$namespace];
        }
        
        $failedCacheKey = sprintf(self::FAILED_CONNECTIONS, $this->getCacheTag(), $role);
        $result = $this->getCache()->load($failedCacheKey);
        $failed = ($result && is_array($result)) ? $result : array();
        
        $servers = $this->getListOfServers($role);
        $keys = (array) array_rand($servers, count($servers));
        shuffle($keys);
        foreach ($keys as $i => $key) {
            if (in_array($key, $failed)) {
                continue;
            }
            $connection = $this->createAdapter($servers[$key], $dbSection);
            if ($connection instanceof Zend_Db_Adapter_Abstract) {
                $this->setAdapter($connection, $role);
                return $connection;
            }
            $failed[] = $key;
            $this->getCache()->save(array_unique($failed), $failedCacheKey, array(), 30);
        }
        throw new Zend_Db_Exception(sprintf('Unable to create adapter "%s" server', $role));
    }
    
    /**
     * 指定されたDBセクションが存在するか判定を行う。
     * 
     * @param string $name
     * @return boolean 存在する場合に正
     */
    public function hasAdapter($name)
    {
        return array_key_exists($name, $this->_adapters);
    }
    
    /**
     * DBアダプターを生成する。
     *
     * @param array $role master (supplier) or slave (consumer)
     * @param string DBセクション
     * @return Zend_Db_Adapter_Abstract
     * @see Zend_Db
     */
    public function createAdapter($role, $dbSection = null)
    {
        // アダプターをCommon_Db::factoryByDbNameで生成しないと
        // Common_Dbのトランザクション機能に影響がでる。
        if ($dbSection) {
            return Common_Db::factoryByDbName($dbSection);
        }
        // MZCL使わない時は↓。
        $config = $this->getConfig();
        foreach ($config as $key => $value) {
            if ('servers' !== $key && !array_key_exists($key, $role)) {
                $role[$key] = $value;
            }
        }
        $db = Zend_Db::factory($config['adapter'], $role);

        return $db;
    }
    
    /**
     * ロール(master or slave)に指定されているサーバーリストを返す。
     * 
     * @param string $role
     * @return array
     */
    public function getListOfServers($role)
    {
        $config = $this->getConfig();
        $servers = (isset($config['servers'])) ? $config['servers'] : array();
        $masterServers = (isset($config['master_servers'])) ? $config['master_servers'] : 1;
        if (self::SUPPLIER_SERVER === $role) {
            $servers = array_slice($servers, 0, $masterServers);
        } elseif (self::CONSUMER_SERVER === $role) {
            $masterRead = (isset($config['master_read'])) ? $config['master_read'] : false;
            if (false === $masterRead) {
                $servers = array_slice($servers, $masterServers, count($servers), true);
            }
        }
        return $servers;
    }
    
    /**
     * デフォルトDBとして設定されているかを判定する。
     * 
     * @return boolean デフォルトDBなら正
     */
    public function isDefault()
    {
        if (isset($this->_config['default']) && $this->_config['default'] == true) {
            return true;
        }
        return false;
    }
}


