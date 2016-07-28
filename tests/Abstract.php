<?php

/**
 * テストケースの基底クラスです。 
 */
abstract class BaseTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    protected function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();

        // 実装側の以下のコードで bootstrap を参照できるようにするためフロントコントローラに明示的にセット
        // <pre>
        // $frontController = Zend_Controller_Front::getInstance();
        // $options         = $frontController->getParam('bootstrap')->getOptions();
        // </pre>
        $this->getFrontController()->setParam('bootstrap', $this->bootstrap);
    }

}
