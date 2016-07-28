<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Zend の ルールから外れるクラスの読み込み設定を登録する
     *
     * @return Zend_Loader_Autoloader_Resource リソースオートローダ
     */
    protected function _initAutoload()
    {
        // 探索基底パスを生成
        $paths    = array(APPLICATION_PATH);
        $basePath = implode(DIRECTORY_SEPARATOR, $paths);

        // Zendのルールから外れるものを登録する。
        // 注意：ディレクトリ名の先頭は大文字にする
        $autoLoader     = Zend_Loader_Autoloader::getInstance();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array('basePath' => $basePath, 'namespace' => ''));
        $resourceLoader->addResourceType('application_model', 'models/', 'Application_Model_');
        $resourceLoader->addResourceType('logics', 'logics/', 'Logic_');
        $resourceLoader->addResourceType('common', 'common/', 'Common_');
        $resourceLoader->addResourceType('misp', 'misp/', 'Misp_');
        $resourceLoader->addResourceType('misp_collection', 'misp/Collection/', 'Misp_Collection');
        $resourceLoader->addResourceType('misp_controller', 'misp/controllers/Base', 'Misp_Base');
        $resourceLoader->addResourceType('misp_akita', 'misp/Akita', 'Akita_');
        $resourceLoader->addResourceType('misp_openid_connect', 'misp/OpenIDConnect', 'OpenIDConnect_');
        $resourceLoader->addResourceType('misp_util', 'misp/Util', 'Misp_');
        $resourceLoader->addResourceType('misp_session_savehandler', 'misp/Session/SaveHandler', 'Misp_Session_SaveHandler');
        $resourceLoader->addResourceType('misp_hybridauth', 'misp/hybridauth/Hybrid', 'Hybrid_');
        $resourceLoader->addResourceType('validate', 'validate/', 'Validate_');

        require_once $basePath . '/misp/OAuth/OAuth.php';

        $libraryPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library');

        $libraryLoader = new Zend_Loader_Autoloader_Resource(array('basePath' => $libraryPath, 'namespace' => ''));
        $libraryLoader->addResourceType('apns', 'ApnsPHP/', 'ApnsPHP_');

        return $autoLoader;
    }

    /**
     * Application.ini の独自設定部分を Zend_Registry に登録する
     */
    protected function _initIni()
    {
        // application.iniの設定を取得
        $options        = new Zend_Config($this->getOptions());
        $dbConfig       = $options->db->toArray();
        $logConfig      = $options->log->toArray();
        $mispConfig     = $options->misp->toArray();

        if ($options->auth) {
            $auth = $options->auth->toArray();
        }

        if ($options->ngWord) {
            $ngWordConfig = $options->ngWord->toArray();
        }

        Zend_Registry::set('database_configs', $dbConfig);
        Zend_Registry::set('log_configs', $logConfig);
        Zend_Registry::set('httpClient_configs', $options->httpClient->toArray());
        Zend_Registry::set('auth_configs', $auth ? $auth : null);
        Zend_Registry::set('misp', $mispConfig);
        Zend_Registry::set('ngWord_configs', $ngWordConfig ? $ngWordConfig : null);
        Zend_Registry::set('cache_configs', $options->cache);
    }

    protected function _initDb()
    {
        try {
            Common_Db::factoryByDbName(); // mainDB
            Common_Db::factoryByDbName('subDb');
        } catch (Exception $e) {
            // TODO Bootstrap.php内ではExceptionを表示させる機能は読み込まれていないため、ここに書きます。
            throw $e;
        }
    }

    /**
     * 共通プラグインで読み込む設定ファイル(YAML)をZend_Registryに登録する
     *
     * @throws Common_Exception_FileNotFound
     */
    protected function _initPluginsYamlParse()
    {
        // 設定ファイル読み込み
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "plugins.yml";

        // 存在しない、読めないは例外
        if (!is_readable($path)) {
            throw new Common_Exception_FileNotFound($path);
        }

        Zend_Registry::set('plugins_configs', yaml_parse_file($path));
    }

    /**
     * プラグインを登録する
     */
    protected function _initRegisterPlugin()
    {
        if (!$this->hasResource('frontController')) {
            $this->bootstrap('frontController');
        }

        $classPrefix  = 'Common_Controller_Plugin_';
        $pluginsNames = array_keys(Zend_Registry::get('plugins_configs'));

        foreach ($pluginsNames as $pluginName) {
            $className = $classPrefix . ucfirst(Common_Util_String::snakeToCamel($pluginName));
            if (class_exists($className)) {
                $this->getResource('frontController')->registerPlugin(new $className, $className::STACK_INDEX);
            }
        }
    }

    /**
     * Route を設定する
     */
    protected function _initRoute()
    {
        $config = new Zend_Config_Ini(implode(DIRECTORY_SEPARATOR, array(APPLICATION_PATH, 'configs', 'routes.ini')));
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->addConfig($config, 'routes');
    }

    /**
     * HybridAuth 設定を読み込む
     */
    protected function _initHybridAuth()
    {
        // 設定ファイル読み込み
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "hybridauth.yml";

        // 存在しない、読めないは例外
        if (!is_readable($path)) {
            throw new Common_Exception_FileNotFound($path);
        }

        $yaml = yaml_parse_file($path);

        Zend_Registry::set('hybridauth_configs', $yaml[APPLICATION_ENV]);
    }

    /**
     * 翻訳ファイルの格納ディレクトリパスを設定する
     */
    protected function _initLanguagePath()
    {
        $path = implode(DIRECTORY_SEPARATOR, array(APPLICATION_PATH, '..', 'language'));
        Zend_Registry::set('language_path', $path);
    }

    /**
     * セッションIDの初期化
     * application.iniでresources.sessionの設定がある場合に実行する
     */
    protected function _initSessionId()
    {
        $router = new Zend_Controller_Router_Rewrite();
        $req    = $router->route(new Zend_Controller_Request_Http());
        // marvelous系Federationコントローラ(HybridAuthで使用するため)、refundモジュールのみセッションを有効化する
        // (ファイルセッションにすると本番環境で不都合があるため)
        if (($req->getModuleName() === "marvelous" && $req->getControllerName() === 'federation') || $req->getModuleName() === "refund") {

            if ($this->hasPluginResource('session')) {
                $this->bootstrap('session');
                $opts = $this->getOptions();
                if ('Common_Session_SaveHandler_Cache' == $opts['resources']['session']['saveHandler']['class']) {
                    $cache = $this->bootstrap('cachemanager')
                            ->getResource('cachemanager')
                            ->getCache('memcached');
                    Zend_Session::getSaveHandler()->setCache($cache);
                }
                $defaultNamespace = new Zend_Session_Namespace();
                if (!isset($defaultNamespace->initialized)) {
                    Zend_Session::regenerateId();
                    $defaultNamespace->initialized = true;
                }
            }
        }
    }

}
