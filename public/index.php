<?php

// エラーレポーティング設定を適宜変更
// APIレスポンスに余計なWarningなどを含めない対応。(含んでしまうとJSONデコードが落ちてしまう)
$org = error_reporting();
error_reporting(E_ERROR);

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../vendor'),
    realpath(APPLICATION_PATH . '/misp'), // Akitaの OpenID Connect ライブラリが require_once 記述しているために、オートローダ起動以前に落ちてしまう対応
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
        APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
        ->run();

// エラーレポーティング設定を元に戻す
error_reporting($org);
