<?php

/**
 * モジュールブートストラップ
 */
class Marvelous_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected function _initTestModule()
    {
        $marvelousLoader = new Zend_Application_Module_Autoloader(array(
            'basePath'  => APPLICATION_PATH . '/modules/marvelous',
            'namespace' => '',
                )
        );

        $marvelousLoader->addResourceType('controller', 'controllers/Base/', 'Marvelous_Base_');

        return $marvelousLoader;
    }

    protected function _initErrorHandler()
    {
        if (!$this->hasResource('frontController')) {
            $this->bootstrap('frontController');
        }
        $this->getResource('frontController')->registerPlugin(new Marvelous_Plugin_ErrorHandler());
    }

}
