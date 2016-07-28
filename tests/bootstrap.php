<?php

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/misp'), // Akitaの OpenID Connect ライブラリが require_once 記述しているために、オートローダ起動以前に落ちてしまう対応
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

require_once 'Abstract.php';

require_once implode(DIRECTORY_SEPARATOR, array(APPLICATION_PATH, 'misp', 'OAuth', 'OAuth.php'));

// オートロード用探索基底パスを生成
$testBasePaths = array(realpath(dirname(__FILE__)), 'application');
$testBasePath  = implode(DIRECTORY_SEPARATOR, $testBasePaths);

// Zendのルールから外れるものを登録する。
$autoLoader     = Zend_Loader_Autoloader::getInstance();
$resourceLoader = new Zend_Loader_Autoloader_Resource(array('basePath'  => $testBasePath, 'namespace' => ''));
$resourceLoader->addResourceType('common', 'common/', 'Common_');

