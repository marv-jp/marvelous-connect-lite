<?php

/**
 * キャッシュ管理クラス
 *
 * キャッシュ管理クラスのクラスです。
 *
 * @category Zend
 * @package Zend_Magic
 * @subpackage Wand
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license   BSD License
 * @version $Id:$
 * @link http://framework.zend.com/package/PackageName
 * @since File available since Release 1.5.0
 */

/**
 * キャッシュ管理クラス
 *
 * キャッシュ管理クラスのクラスです。
 *
 * @category Zend
 * @package Zend_Magic
 * @subpackage Wand
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license   BSD License
 * @version Release: @package_version@
 * @link http://framework.zend.com/package/PackageName
 * @since Class available since Release 1.5.0
 */
class Common_Cache
{
    /** @var String Memcacheのバックエンド名 */
    const BACKEND_MEMCACHE = 'memcache';

    /** @var String APCのバックエンド名 */
    const BACKEND_APC = 'apc';

    /** @var String Couchbaseのバックエンド名 */
    const BACKEND_COUCHBASE = 'couchbase';

    /** @var Common_Cache キャッシュ管理クラスのインスタンス */
    private static $instance = null;

    /** @var array キャッシュバックエンドインスタンス格納配列 */
    private static $backends = array();

    /**
     * キャッシュ管理クラスのインスタンスを返す。
     * 
     * キャッシュ操作の各インスタンスを取り出す場合は、このメソッド経由で取得すること。
     * 
     * @return Common_Cache キャッシュ管理インスタンス
     */
    public static function getInstance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new Common_Cache();
        }
        return self::$instance;
    }

    /**
     * Memcacheを操作するインスタンスを返す。
     * 
     * @return Zend_Cache_Core
     */
    public function getMemcache()
    {
        if (isset(self::$backends[self::BACKEND_MEMCACHE]))
        {
            return self::$backends[self::BACKEND_MEMCACHE];
        }

        $appCacheConfig = Zend_Registry::get('cache_configs');

        $config = $appCacheConfig->toArray();
        $backendOptions['servers'] = array();
        foreach ($config['memcache'] as $memcache)
        {
            $backendOptions['servers'][] = $memcache;
        }

        self::$backends[self::BACKEND_MEMCACHE] = Zend_Cache::factory('Core', 'Memcached', $config['frontend'], $backendOptions);
        return self::$backends[self::BACKEND_MEMCACHE];
    }

    /**
     * APCを操作するインスタンスを返す。
     * 
     * @return Zend_Cache_Core
     */
    public function getApc()
    {
        if (isset(self::$backends[self::BACKEND_APC]))
        {
            return self::$backends[self::BACKEND_APC];
        }

        $appCacheConfig = Zend_Registry::get('cache_configs');
        $config = $appCacheConfig->toArray();
        self::$backends[self::BACKEND_APC] = Zend_Cache::factory('Core', 'Apc', $config['frontend']);
        return self::$backends[self::BACKEND_APC];
    }

    /**
     * Couchbaseを操作するインスタンスを返す。
     * 
     * <b>application.iniの設定例</b>
     * <pre>
     * ;cache.couchbase.[server_id].[各オプション名] = [設定値]
     * ;user,bucketには両方共バケット名を設定
     * cache.couchbase.0.host      = "127.0.0.1"
     * cache.couchbase.0.port      = "8091"
     * cache.couchbase.0.user      = "default"
     * cache.couchbase.0.password  = ""
     * cache.couchbase.0.bucket    = "default"
     * cache.couchbase.1.host      = "127.0.0.1"
     * cache.couchbase.1.port      = "8091"
     * cache.couchbase.1.user      = "hoge"
     * cache.couchbase.1.password  = ""
     * cache.couchbase.1.bucket    = "hoge"
     * </pre>
     * 
     * <b>使い方</b>
     * <pre>
     * $cache = Common_Cache::getInstance()->getCouchbase();
     * 
     * // デフォルトはserver_idが0のものが使用される
     * $cache->save('piyo', 'bar');
     * var_dump($cache->load('bar'));
     * 
     * // 接続先を切り替える
     * $cache->getBackend()->setOption('server_id', 1);
     * $cache->save(array('foo' => 'bar', 'piyo' => 10), 'bar');
     * var_dump($cache->load('bar'));
     * </pre>
     * 
     * @return Zend_Cache_Core
     */
    public function getCouchbase()
    {
        if (isset(self::$backends[self::BACKEND_COUCHBASE]))
        {
            return self::$backends[self::BACKEND_COUCHBASE];
        }

        $appCacheConfig = Zend_Registry::get('cache_configs');

        $config = $appCacheConfig->toArray();
        $backendOptions['servers'] = array();
        foreach ($config['couchbase'] as $couchbase)
        {
            $backendOptions['servers'][] = $couchbase;
        }

        $frontendConf = $config['frontend'];
        
        // Coreでdataは文字列しか許容しないが、Couchbaseの管理画面ではシリアライズしてほしくない
        // バックエンドでデータのシリアライズ状況を制御するため強制的にtrueにする
        $frontendConf['automatic_serialization'] = true;
        
        self::$backends[self::BACKEND_COUCHBASE] = Zend_Cache::factory('Core', 'Common_Cache_Backend_Couchbase', $frontendConf, $backendOptions, false, true, true);
        return self::$backends[self::BACKEND_COUCHBASE];
    }

    /**
     * 外部からこのクラスをnewされないようにprivateでコンストラクタを定義
     */
    private function __construct()
    {
        
    }

}
