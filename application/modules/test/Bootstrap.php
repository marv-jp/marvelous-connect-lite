<?php

/**
 * モジュールブートストラップ
 */
class Test_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected function _initTestModule()
    {
        $testLoader = new Zend_Application_Module_Autoloader(array(
            'basePath'  => APPLICATION_PATH . '/modules/test',
            'namespace' => '',
                )
        );

        $testLoader->addResourceType('controller', 'controllers/Base/', 'Test_Base_');

        return $testLoader;
    }   

}
