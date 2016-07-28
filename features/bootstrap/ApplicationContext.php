<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class ApplicationContext extends BehatContext
{

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^"アプリケーション検証"が呼び出される$/
     */
    public function zhouBox()
    {
        $context = $this->getMainContext();
        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['sub'];

            $logic  = new Logic_Application();
            $logic->setApplicationMapper(array($applicationDb => $context->getParam('applicationMapper')));
            $return = $logic->isValidApplication($context->getParam('model'));
            $context->setReturn($return);
        }
        catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"アプリケーション取得"が呼び出される$/
     */
    public function box()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['sub'];;

            $logic  = new Logic_Application();
            $logic->setApplicationMapper(array($applicationDb => $context->getParam('applicationMapper')));
            $return = $logic->readApplication($context->getParam('model'));
            $context->setReturn($return);
        }
        catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

}