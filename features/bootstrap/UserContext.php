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
class UserContext extends BehatContext
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
     * @Given /^: ユーザ更新を実行する$/
     */
    public function chan2()
    {
        $context     = $this->getMainContext();
        $mockContext = $this->getMainContext()->getSubcontext('Mock_alias');

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setUserMapper(array($applicationDb => $mockContext->getMockByNum(1)));
            $return = $logic->updateUser($context->getArg(1));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"ユーザプラットフォームアプリケーション関連取得"が呼び出される$/
     */
    public function fengQiBox()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $context->getParam('userPlatformApplicationRelationMapper')));
            $logic->setApplicationMapper(array($applicationDb => $context->getParam('applicationMapper')));
            $return = $logic->readUserPlatformApplicationRelation($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される$/
     */
    public function fengQiDaenhZhouBox()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $context->getParam('userPlatformApplicationRelationMapper')));
            $logic->setApplicationMapper(array($applicationDb => $context->getParam('applicationMapper')));
            $return = $logic->readUserPlatformApplicationRelationWithValidate($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^: ユーザプラットフォームアプリケーション関連更新を実行する$/
     */
    public function fengQiChan()
    {
        $context     = $this->getMainContext();
        $mockContext = $this->getMainContext()->getSubcontext('Mock_alias');

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $mockContext->getMockByNum(1)));
            $return = $logic->updateUserPlatformApplicationRelation($context->getArg(1));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^: アプリケーションユーザプラットフォーム関連更新を実行する$/
     */
    public function fengQiChanA()
    {
        $context     = $this->getMainContext();
        $mockContext = $this->getMainContext()->getSubcontext('Mock_alias');

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setApplicationUserPlatformRelationMapper(array($applicationDb => $mockContext->getMockByNum(1)));
            $return = $logic->updateApplicationUserPlatformRelation($context->getArg(1));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^: プラットフォームユーザ更新を実行する$/
     */
    public function chan()
    {
        $context     = $this->getMainContext();
        $mockContext = $this->getMainContext()->getSubcontext('Mock_alias');

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setPlatformUserMapper(array($applicationDb => $mockContext->getMockByNum(1)));
            $return = $logic->updatePlatformUser($context->getArg(1));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"ユーザ取得"が呼び出される$/
     */
    public function box()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic = new Logic_User();

            $logic->setApplicationUserPlatformRelationMapper(array($applicationDb => $context->getParam('applicationUserPlatformRelationMapper')));
            $logic->setApplicationUserMapper(array($applicationDb => $context->getParam('applicationUserMapper')));
            $logic->setPlatformUserMapper(array($applicationDb => $context->getParam('platformUserMapper')));
            $logic->setUserMapper(array($applicationDb => $context->getParam('userMapper')));
            $logic->setUserLogic($context->getParam('logicUser'));
            $return = $logic->readUser($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"ID連携状態確認処理"が呼び出される$/
     */
    public function idqiDanXuanHeLiaBox()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $context->getParam('userPlatformApplicationRelationMapper')));
            $logic->setUserLogic($context->getParam('logicUser'));
            $return = $logic->readIdFederationStatus($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"プラットフォーム認証後処理"が呼び出される$/
     */
    public function heZhouLiaBox()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setUserLogic($context->getParam('logicUser'));
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $context->getParam('userPlatformApplicationRelationMapper')));
            $logic->setPlatformUserMapper(array($applicationDb => $context->getParam('platformUserMapper')));
            $logic->setApplicationMapper(array($applicationDb => $context->getParam('applicationMapper')));
            $logic->setUserMapper(array($applicationDb => $context->getParam('userMapper')));
            $return = $logic->federationCallback($context->getParam('model'), $context->getParam('model2'), $context->getParam('model3'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"ユーザ登録"が呼び出される$/
     */
    public function fouZhangBox2()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setUserMapper(array($applicationDb => $context->getParam('userMapper')));
            $return = $logic->createUser();
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"ユーザプラットフォームアプリケーション関連登録"が呼び出される$/
     */
    public function fengQiFouZhangBox2()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $context->getParam('userPlatformApplicationRelationMapper')));
            $return = $logic->createUserPlatformApplicationRelation($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"アプリケーションユーザプラットフォーム関連登録"が呼び出される$/
     */
    public function fengQiFouZhangBox()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setApplicationUserPlatformRelationMapper(array($applicationDb => $context->getParam('applicationUserPlatformRelationMapper')));
            $return = $logic->createApplicationUserPlatformRelation($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"プラットフォームユーザ登録"が呼び出される$/
     */
    public function fouZhangBox()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setPlatformMapper(array($applicationDb => $context->getParam('platformMapper')));
            $logic->setPlatformUserMapper(array($applicationDb => $context->getParam('platformUserMapper')));
            $return = $logic->createPlatformUser($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^: 連携解除通知リクエスト時処理を実行する$/
     */
    public function qiJiMangChuanJuLiaChan()
    {
        $context     = $this->getMainContext();
        $mockContext = $this->getMainContext()->getSubcontext('Mock_alias');

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;
            $logic         = new Logic_User();
            $logic->setUserLogic($mockContext->getMockByNum(1));
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $mockContext->getMockByNum(2)));
            $return        = $logic->cancelPlatformUserFederation($context->getArg(1));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^: ID連携解除時処理を実行する$/
     */
    public function idqiJiMangLiaChan()
    {
        $context     = $this->getMainContext();
        $mockContext = $this->getMainContext()->getSubcontext('Mock_alias');

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];
            ;

            $logic  = new Logic_User();
            $logic->setUserLogic($mockContext->getMockByNum(1));
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $mockContext->getMockByNum(2)));
            $return = $logic->cancelIdFederation($context->getArg(1));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"ID連携処理"が呼び出される$/
     */
    public function idqiLiaBox()
    {
        $context = $this->getMainContext();
        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];

            $logic  = new Logic_User();
            $logic->setUserLogic($context->getParam('logicUser'));
            $logic->setApplicationUserLogic($context->getParam('logicApplicationUser'));
            $logic->setApplicationMapper(array($applicationDb => $context->getParam('applicationMapper')));
            $logic->setApplicationUserMapper(array($applicationDb => $context->getParam('applicationUserMapper')));
            $logic->setApplicationUserPlatformRelationMapper(array($applicationDb => $context->getParam('applicationUserPlatformRelationMapper')));
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $context->getParam('userPlatformApplicationRelationMapper')));
            $logic->setMispApiMode($context->getParam('apiMode'));
            $return = $logic->createIdFederation($context->getParam('model'), $context->getParam('model2'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"ユーザ検証"が呼び出される$/
     */
    public function zhouBox()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];

            $logic  = new Logic_User();
            $logic->setUserLogic($context->getParam('logicUser'));
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $context->getParam('userPlatformApplicationRelationMapper')));
            $return = $logic->isValidUser($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"プラットフォーム取得"が呼び出される$/
     */
    public function boxusss()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['sub'];

            $logic  = new Logic_User();
            $logic->setUserLogic($context->getParam('logicUser'));
            $logic->setPlatformMapper(array($applicationDb => $context->getParam('platformMapper')));
            $return = $logic->readPlatform($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"プラットフォームユーザ取得"が呼び出される$/
     */
    public function boxufdsaoihfew()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['sub'];

            $logic  = new Logic_User();
            $logic->setUserLogic($context->getParam('logicUser'));
            $logic->setPlatformUserMapper(array($applicationDb => $context->getParam('platformUserMapper')));
            $return = $logic->readPlatformUser($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"認可コードによるトークン取得"が呼び出される$/
     */
    public function basicBox()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];

            $logic  = new Logic_User();
            $logic->setUserLogic($context->getParam('logicUser'));
            $logic->setApplicationRedirectUriMapper(array($applicationDb => $context->getParam('applicationRedirectUriMapper')));
            $logic->setUserPlatformApplicationRelationMapper(array($applicationDb => $context->getParam('userPlatformApplicationRelationMapper')));
            $return = $logic->readTokenForBasic($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"リフレッシュトークンによるトークン再取得"が呼び出される$/
     */
    public function boxForRefreshToken()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['main'];

            $logicUser = new Logic_User();
            $logicUser->setUserLogic($context->getParam('logicUser'));
            $logicUser->setApplicationLogic($context->getParam('logicApplication'));
            $logicUser->setUserPlatformApplicationRelationMapper(array($applicationDb => $context->getParam('userPlatformApplicationRelationMapper')));
            $return    = $logicUser->readTokenForRefreshToken($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"リダイレクトURI検証"が呼び出される$/
     */
    public function uriZhouBox()
    {
        $context = $this->getMainContext();

        try {
            $config        = Zend_Registry::get('misp');
            $applicationDb = $config['db']['sub'];

            $logic  = new Logic_User();
            $logic->setApplicationRedirectUriMapper(array($applicationDb => $context->getParam('applicationRedirectUriMapper')));
            $return = $logic->isValidRedirectUri($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^"プラットフォーム情報取得（キャッシュ版）"が呼び出される$/
     */
    public function daelYaoDaelgBox()
    {
        $context = $this->getMainContext();

        try {

            $logic  = new Logic_User();
            $logic->setUserLogic($context->getParam('logicUser'));
            $return = $logic->readPlatformWithCache($context->getParam('model'));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

}
