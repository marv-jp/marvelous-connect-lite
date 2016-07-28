<?php

class App_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected function _initAppModule()
    {
        /*
         * モジュール用の初期化処理を行う
         */

        $adminLoader = new Zend_Application_Module_Autoloader(array(
                    'basePath'  => APPLICATION_PATH . '/modules/app',
                    'namespace' => '',
                        )
        );

        $adminLoader->addResourceType('controller', 'controllers/', 'App_');

        return $adminLoader;
    }
 
}
