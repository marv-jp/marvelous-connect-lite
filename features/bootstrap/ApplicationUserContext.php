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
class ApplicationUserContext extends BehatContext
{

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        
    }

    /**
     * @Given /^"アプリケーションユーザ認証"が呼び出される$/
     */
    public function heZhouBox()
    {
        $context = $this->getMainContext();
        try {
            $config            = Zend_Registry::get('misp');
            $applicationUserDb = $config['db']['main'];
            $logic  = new Logic_ApplicationUser();
            $logic->setApplicationUserMapper(array($applicationUserDb => $context->getParam('applicationUserMapper')));
            $logic->setApplicationUserLogic($context->getParam('logicApplicationUser'));
            $return = $logic->authenticateApplicationUser($context->getParam('model'), $context->getParam('model2'), $context->getParam('model3'), $context->getArg(4));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"アプリケーションユーザ登録"が呼び出される$/
     */
    public function fouZhangBox()
    {
        $context = $this->getMainContext();
        try {
            $config            = Zend_Registry::get('misp');
            $applicationUserDb = $config['db']['main'];
            ;

            $logic = new Logic_ApplicationUser();
            $logic->setApplicationUserMapper(array($applicationUserDb => $context->getParam('applicationUserMapper')));

            $return = $logic->createApplicationUser($context->getParam('model'));

            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"アプリケーションユーザ取得"が呼び出される$/
     */
    public function box()
    {
        $context = $this->getMainContext();
        try {

            $config            = Zend_Registry::get('misp');
            $applicationUserDb = $config['db']['main'];

            $logic = new Logic_ApplicationUser();
            $logic->setApplicationUserMapper(array($applicationUserDb => $context->getParam('applicationUserMapper')));

            $return = $logic->readApplicationUser($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"アプリケーションユーザ更新"が呼び出される$/
     */
    public function box2()
    {
        $context = $this->getMainContext();
        try {
            $config            = Zend_Registry::get('misp');
            $applicationUserDb = $config['db']['main'];
            ;

            $logicApplicationUser = new Logic_ApplicationUser();
            $logicApplicationUser->setApplicationUserMapper(array($applicationUserDb => $context->getParam('applicationUserMapper')));
            $return               = $logicApplicationUser->updateApplicationUser($context->getParam('model'));

            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

}
