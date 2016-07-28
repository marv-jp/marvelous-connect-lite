<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Mock context.
 */
class MockContext extends BehatContext
{
    // モック(ズ)
    private $_mocks;

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
     * 指定された番号のモックオブジェクトを返します。
     * 
     * @param integer $num
     * @return PHPUnit_Framework_MockObject_Builder_InvocationMocker モックオブジェクト
     */
    public function getMockByNum($num)
    {
        return $this->_mocks[$num];
    }

    /**
     * @Given /^モック"Application_Model_ApplicationMapper->find"で異常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationmapperFindLanBiLia()
    {

        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationMapper');
        $mapper->expects($mock->any())
                ->method('find')
                ->will($mock->returnValue(NULL));
        $context = $this->getMainContext();
        $context->setParam('applicationMapper', $mapper);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationMapper->find"で正常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationmapperFindBiLia()
    {
        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationMapper');
        $mapper->expects($mock->any())
                ->method('find')
                ->will($mock->returnValue(new Application_Model_Application()));
        $context = $this->getMainContext();
        $context->setParam('applicationMapper', $mapper);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationMapper->fetchAll"で正常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationmapperFetchallBiLia()
    {
        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationMapper');
        $mapper->expects($mock->any())
                ->method('fetchAll')
                ->will($mock->returnValue(new Application_Model_Application()));
        $context = $this->getMainContext();
        $context->setParam('applicationMapper', $mapper);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationMapper->fetchAll"で異常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationmapperFetchallLanBiLia()
    {
        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationMapper');
        $mapper->expects($mock->any())
                ->method('fetchAll')
                ->will($mock->returnValue(NULL));
        $context = $this->getMainContext();
        $context->setParam('applicationMapper', $mapper);
    }

    /**
     * @Given /^モック"Application->isValidApplication"で正常値が返ってくる処理がセットされる$/
     */
    public function applicationIsvalidapplicationBiLia()
    {
        $mock    = new BehatMock();
        $logic   = $mock->getMock('Logic_Application');
        $logic->expects($mock->any())
                ->method('isValidApplication')
                ->will($mock->returnValue(TRUE));
        $context = $this->getMainContext();
        $context->setParam('logicApplication', $logic);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationUserMapper->find"で正常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationusermapperFindBiLia()
    {
        $response = new Application_Model_ApplicationUser();
        $response->setApplicationId('mainAppID');
        $response->setApplicationUserId('一意のアプリケーションユーザID');
        $response->setPassword('正しいパスワード');
        $response->setStatus(1);
        $response->setCreatedDate('2013/11/11 11:11:11');
        $response->setUpdatedDate('2013/11/11 11:11:11');

        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationUserMapper');
        $mapper->expects($mock->any())
                ->method('find')
                ->will($mock->returnValue($response));
        $context = $this->getMainContext();
        $context->setParam('applicationUserMapper', $mapper);
    }

    /**
     * @Given /^モック"ApplicationUser->updateApplicationUser"で正常値が返ってくる処理がセットされる$/
     */
    public function applicationuserUpdateapplicationuserBiLia()
    {
        $mock    = new BehatMock();
        $logic   = $mock->getMock('Logic_ApplicationUser');
        $logic->expects($mock->any())
                ->method('updateApplicationUser')
                ->will($mock->returnValue(TRUE));
        $context = $this->getMainContext();
        $context->setParam('logicApplicationUser', $logic);
    }

    /**
     * @Given /^モック"Application->isValidApplication"で異常値が返ってくる処理がセットされる$/
     */
    public function applicationIsvalidapplicationLanBiLia()
    {
        $mock    = new BehatMock();
        $logic   = $mock->getMock('Logic_Application');
        $logic->expects($mock->any())
                ->method('isValidApplication')
                ->will($mock->throwException(new Common_Exception_AuthenticationFailed));
        $context = $this->getMainContext();
        $context->setParam('logicApplication', $logic);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationUserMapper->find"で異常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationusermapperFindLanBiLia()
    {
        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationUserMapper');
        $mapper->expects($mock->any())
                ->method('find')
                ->will($mock->returnValue(NULL));
        $context = $this->getMainContext();
        $context->setParam('applicationUserMapper', $mapper);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationUserMapper->find"で異常値が返ってきて、"Application_Model_ApplicationUserMapper->insert"で正常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationusermapperFindLanBiApplicationModelApplicationusermapperInsertBiLia()
    {
        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationUserMapper');
        $mapper->expects($mock->any())
                ->method('find')
                ->will($mock->returnValue(NULL));
        $mapper->expects($mock->any())
                ->method('insert')
                ->will($mock->returnValue(1));
        $context = $this->getMainContext();
        $context->setParam('applicationUserMapper', $mapper);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationUserMapper->find"で正常値が返ってきて、"Application_Model_ApplicationUserMapper->update"で異常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationusermapperFindBiApplicationModelApplicationusermapperUpdateLanBiLia()
    {
        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationUserMapper');
        $mapper->expects($mock->any())
                ->method('find')
                ->will($mock->returnValue(new Application_Model_ApplicationUser()));
        $mapper->expects($mock->any())
                ->method('update')
                ->will($mock->returnValue(0));
        $context = $this->getMainContext();
        $context->setParam('applicationUserMapper', $mapper);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationUserMapper->insert"で(\d+)が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationusermapperInsertBiLia($arg1)
    {
        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationUserMapper');
        $mapper->expects($mock->any())
                ->method('insert')
                ->will($mock->returnValue($arg1));
        $context = $this->getMainContext();
        $context->setParam('applicationUserMapper', $mapper);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationUserMapper->update"で正常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationusermapperUpdateBiLia()
    {
        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationUserMapper');
        $mapper->expects($mock->any())
                ->method('update')
                ->will($mock->returnValue(new Application_Model_ApplicationUser()));
        $context = $this->getMainContext();
        $context->setParam('applicationUserMapper', $mapper);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationUserMapper->find"で正常値が返ってきて、"Application_Model_ApplicationUserMapper->update"で正常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationusermapperFindBiApplicationModelApplicationusermapperUpdateBiLia()
    {
        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationUserMapper');
        $mapper->expects($mock->any())
                ->method('find')
                ->will($mock->returnValue(new Application_Model_ApplicationUser()));
        $mapper->expects($mock->any())
                ->method('update')
                ->will($mock->returnValue(1));
        $context = $this->getMainContext();
        $context->setParam('applicationUserMapper', $mapper);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationUserMapper->fetchAll"で正常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationusermapperFetchallBiLia()
    {
        $responseModel = new Application_Model_ApplicationUser();
        $responseModel->setApplicationId('mainAppID');
        $responseModel->setApplicationUserId('一意のアプリケーションユーザID');
        $responseModel->setPassword('正しいパスワード');
        $responseModel->setStatus(1);
        $responseModel->setCreatedDate('2013/11/11 11:11:11');
        $responseModel->setUpdatedDate('2013/11/11 11:11:11');

        $response = array($responseModel);

        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationUserMapper');
        $mapper->expects($mock->any())
                ->method('fetchAll')
                ->will($mock->returnValue($response));
        $context = $this->getMainContext();
        $context->setParam('applicationUserMapper', $mapper);
    }

    /**
     * @Given /^モック"Application_Model_ApplicationUserMapper->fetchAll"で異常値が返ってくる処理がセットされる$/
     */
    public function applicationModelApplicationusermapperFetchallLanBiLia()
    {
        $mock    = new BehatMock();
        $mapper  = $mock->getMock('Application_Model_ApplicationUserMapper');
        $mapper->expects($mock->any())
                ->method('fetchAll')
                ->will($mock->returnValue(0));
        $context = $this->getMainContext();
        $context->setParam('applicationUserMapper', $mapper);
    }

    /**
     * @Given /^: モック(\d+)は "([^"]*)" メソッドで "([^"]*)" クラスのオブジェクトを返却する$/
     */
    public function vBi2($arg1, $arg2, $arg3)
    {
        $argNum    = $arg1;
        $method    = $arg2;
        $className = $arg3;

        $m    = new BehatMock();
        $mock = $this->getMockByNum($argNum);
        $mock->expects($m->any())
                ->method($method)
                ->will($m->returnValue(new $className()));
    }

    /**
     * @Given /^: モック(\d+)は "([^"]*)" メソッドで "([^"]*)" 型の "([^"]*)" を返却する$/
     */
    public function vNfBi2($arg1, $arg2, $arg3, $arg4)
    {
        $argNum = $arg1;
        $method = $arg2;
        $type   = $arg3;
        $value  = $arg4;

        settype($value, $type);

        $m    = new BehatMock();
        $mock = $this->getMockByNum($argNum);
        $mock->expects($m->any())
                ->method($method)
                ->will($m->returnValue($value));
    }

    /**
     * @Given /^: モック(\d+)は "([^"]*)" メソッドで空の配列を返却する$/
     */
    public function vSinKuaiBi($arg1, $arg2)
    {
        $argNum = $arg1;
        $method = $arg2;

        $m    = new BehatMock();
        $mock = $this->getMockByNum($argNum);
        $mock->expects($m->any())
                ->method($method)
                ->will($m->returnValue(array()));
    }

    /**
     * @Given /^モックとして "([^"]*)" クラスを "([^"]*)" として使用する$/
     */
    public function zhi2($arg1, $arg2)
    {
        $mock      = new BehatMock();
        $mockClass = $mock->getMock($arg1);
        $context   = $this->getMainContext();
        $context->setParam($arg2 . 'Mock', $mock);
        $context->setParam($arg2, $mockClass);
    }

    /**
     * @Given /^モックとして "([^"]*)" クラスを "([^"]*)" として使用する（引数は "([^"]*)" ）$/
     */
    public function zhiDdfslaaelDaelg($arg1, $arg2, $arg3)
    {
        $context   = $this->getMainContext();
        $mock      = new BehatMock();
        $mockClass = $mock->getMock($arg1, array(), array(new $arg3()));
        $context->setParam($arg2 . 'Mock', $mock);
        $context->setParam($arg2, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" を "([^"]*)" の "([^"]*)" の引数としてセット$/
     */
    public function stepDefinition8($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();
        $arg2::$arg3($context->getParam($arg1));
    }

    /**
     * @Given /^モックとして "([^"]*)" クラスを "([^"]*)" として使用する（引数は "([^"]*)" , "([^"]*)" ）$/
     */
    public function zhiDaelDaelg($arg1, $arg2, $arg3, $arg4)
    {
        $mock      = new BehatMock();
        $mockClass = $mock->getMock($arg1, array(), array(new $arg3(), $this->$arg4()));
        $context   = $this->getMainContext();
        $context->setParam($arg2 . 'Mock', $mock);
        $context->setParam($arg2, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" で "([^"]*)" の文字列の値が返ってくる処理がセットされる$/
     */
    public function stringagaBiLia($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = $arg3;

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でboolean値の "([^"]*)" が返ってくる処理がセットされる$/
     */
    public function booleanBiLia($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = (boolean) $arg3;

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "([^"]*)" ）$/
     */
    public function kuaiBiLiaDaelChaDaelg($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return   = array();
        $return[] = $this->$arg3();

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "([^"]*)" 配列の長さは(\d+)）$/
     */
    public function kuaiBiLiaDaelChaKuaiDaelg($arg1, $arg2, $arg3, $arg4)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = $this->$arg3($arg4);

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" で例外 "([^"]*)" が返ってくる処理がセットされる$/
     */
    public function biLia3($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->throwException(new $arg3()));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でモデル "([^"]*)" が返ってくる処理がセットされる（モデルの中身は "([^"]*)" ）$/
     */
    public function biLiaDaelChaDaelg($arg1, $arg2, $arg3, $arg4)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = $this->$arg4();

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でコレクション "([^"]*)" が返ってくる処理がセットされる（モデルの中身は "([^"]*)" ）$/
     */
    public function biLiaDaekolCmmohaDaelg($arg1, $arg2, $arg3, $arg4)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // コレクションにセットするモデル
        $setModel = $this->$arg4();

        // コレクションにアタッチ
        $returnCollection = new $arg3();

        $returnCollection->attach($setModel);
        $returnCollection->rewind();

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($returnCollection));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" で空の配列が返ってくる処理がセットされる$/
     */
    public function sinKuaiBiLia($arg1, $arg2)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = array();

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" で(\d+)件ヒットが返ってくる処理がセットされる$/
     */
    public function biLia($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = $arg3;

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でインサートID (\d+) が返ってくる処理がセットされる$/
     */
    public function idBiLia($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = $arg3;

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でboolean型の "([^"]*)" が返ってくる処理がセットされる$/
     */
    public function booleannfBiLia($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = FALSE;

        if ('true' == $arg3) {
            $return = TRUE;
        }

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^: モック(\d+)は "([^"]*)" メソッドで "([^"]*)" クラスのオブジェクトを (\d+) つ配列で返却する$/
     */
    public function vKuaiBi($arg1, $arg2, $arg3, $arg4)
    {
        $argNum    = $arg1;
        $method    = $arg2;
        $className = $arg3;
        $num       = $arg4;

        $objects = array();
        for ($i = 0; $i < $num; $i++) {
            $objects[] = new $className();
        }

        $m    = new BehatMock();
        $mock = $this->getMockByNum($argNum);
        $mock->expects($m->any())
                ->method($method)
                ->will($m->returnValue($objects));
    }

    /**
     * @Given /^: モック(\d+)は "([^"]*)" 型である$/
     */
    public function nf3($arg1, $arg2)
    {
        $argNum   = $arg1;
        $mockType = $arg2;

        $m                     = new BehatMock();
        $this->_mocks[$argNum] = $m->getMock($mockType);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" で例外 "([^"]*)" が投げられる処理がセットされる$/
     */
    public function lia2($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->throwException(new $arg3()));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" で以下のメソッドの戻り値が返ってくる処理をセット "([^"]*)" , "([^"]*)"$/
     */
    public function vBiLia($arg1, $arg2, $arg3, $arg4)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->onConsecutiveCalls($this->$arg3(), $this->$arg4()));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" で "([^"]*)" が "([^"]*)" メソッドで "([^"]*)" を返すモック\(引数は "([^"]*)" \)が返ってくる処理がセットされる$/
     */
    public function vBiBiLia($arg1, $arg2, $arg3, $arg4, $arg5, $arg6)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値としてのモックを生成する
        $returnMock = new BehatMock();

        if ($arg6) {
            $returnMockClass = $returnMock->getMock($arg3, array(), array($this->$arg6()));
        } else {
            $returnMockClass = $returnMock->getMock($arg3);
        }

        $returnMockClass->expects($returnMock->any())
                ->method($arg4)
                ->will($returnMock->returnValue($this->returnCast($arg5)));


        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($returnMockClass));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" で "([^"]*)" が "([^"]*)" メソッドで "([^"]*)" を返すモック\(引数は配列\)が返ってくる処理がセットされる$/
     */
    public function vBiKuaiBiLia($arg1, $arg2, $arg3, $arg4, $arg5)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値としてのモックを生成する
        $returnMock = new BehatMock();

        $returnMockClass = $returnMock->getMock($arg3, array(), array(array()));

        $returnMockClass->expects($returnMock->any())
                ->method($arg4)
                ->will($returnMock->returnValue($this->returnCast($arg5)));


        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($returnMockClass));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    public function returnCast($foo)
    {
        $string = mb_strtolower($foo);

        switch ($string) {
            case "true":
                return TRUE;
            case "false":
                return FALSE;
            default:
                return $foo;
        }
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でフィクスチャ "([^"]*)" の "([^"]*)" が返ってくる処理がセットされる$/
     */
    public function bifixtureLia2($arg1, $arg2, $arg3, $arg4)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $fixtures         = new $arg3();
        $fixturesProperty = 'get' . ucfirst($arg4) . 'Set';

        $return = $fixtures->$fixturesProperty;

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" で "([^"]*)" モックを返すモックが返ってくる処理がセットされる$/
     */
    public function biczdvsBiLia($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = $context->getParam($arg3);

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" のプロパティ "([^"]*)" にはメソッド "([^"]*)" の結果が返却される$/
     */
    public function vHuanBi($arg1, $arg2, $arg3)
    {
        // モック取得
        $context   = $this->getMainContext();
        $mockClass = $context->getParam($arg1);
        $setMethod = Common_Util_String::snakeToCamel('set_' . $arg2);
        $mockClass->$setMethod($this->$arg3());
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でコレクション "([^"]*)" （ "([^"]*)" が呼びだされている）が返ってくる処理がセットされる（モデルの中身は "([^"]*)" ）$/
     */
    public function daelBoxDaelgBiLiaDaelChaDaelg($arg1, $arg2, $arg3, $arg4, $arg5)
    {
        // モック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = new $arg3;
        $return->$arg4();
        $return->attach($this->$arg5());
        $return->rewind();
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" を複数回呼んだ時にコレクションが返ってくる処理がセットされる \(返ってくるコレクションの配列は "([^"]*)" \)$/
     */
    public function xianSoboBiLiaBiKuai($arg1, $arg2, $arg3)
    {
        // モック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値        
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will(call_user_func_array(array($mock, 'onConsecutiveCalls'), $this->$arg3()));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でコレクション "([^"]*)" （ "([^"]*)" が呼びだされている）\( "([^"]*)" は "([^"]*)" \)が返ってくる処理がセットされる（モデルの中身は "([^"]*)" ）$/
     */
    public function daelBoxDaelgBiLiaDaelChaDaelgd($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
    {
        // モック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = new $arg3;
        $return->$arg4();
        $method = 'set' . ucfirst($arg5);
        $return->$method($arg6);
        $return->attach($this->$arg7());
        $return->rewind();
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)"  で例外 "([^"]*)" が投げられる処理がセットされる$/
     */
    public function lia2lia2($arg1, $arg2, $arg3)
    {
        // モック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->throwException(new $arg3));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でモデルが返ってくる処理がセットされる（モデルの中身は "([^"]*)" ）$/
     */
    public function biLiaDaelChaDaelgpopop($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // 戻り値
        $return = $this->$arg3();

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($return));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック用として "([^"]*)" に "([^"]*)" クラスを用意$/
     */
    public function zhiZhi($arg1, $arg2)
    {
        $context = $this->getMainContext();
        $context->setParam($arg1, new $arg2());
    }

    /**
     * @Given /^モック用 "([^"]*)" のメソッド "([^"]*)" が呼ばれる（引数 "([^"]*)" ）$/
     */
    public function zhiVBoDaelDaelg($arg1, $arg2, $arg3)
    {
        $context  = $this->getMainContext();
        $mockData = $context->getParam($arg1);

        // 引数がある場合はそれを使用、空文字の場合は使用しない
        if ($arg3) {
            $mockData->$arg2($arg3);
        } else {
            $mockData->$arg2();
        }

        $context->setParam($arg1, $mockData);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" でモック用 "([^"]*)" が返ってくる処理がセットされる$/
     */
    public function zhiBiLia($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($context->getParam($arg3)));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" メソッドで変数 "([^"]*)" が返ってくるようモックを設定$/
     */
    public function vBiZhao($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($context->getValue($arg3)));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

    public function getPlatformModel()
    {
        $returnModel = new Application_Model_Platform();
        $returnModel->setPlatformName('Facebook');
        $returnModel->setPlatformId('プラットフォームID');
        $returnModel->setPlatformType(Application_Model_Platform::PLATFORM_TYPE_MAIN);
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getPlatformUserModel()
    {
        $returnModel = new Application_Model_PlatformUser();
        $returnModel->setPlatformId('プラットフォームID');
        $returnModel->setPlatformUserId('プラットフォームユーザID');
        $returnModel->setPlatformUserName('プラットフォームユーザ名');
        $returnModel->setPlatformUserDisplayName('プラットフォームユーザ表示名');
        $returnModel->setAccessToken('アクセストークン');
        $returnModel->setIdToken('IDトークン');
        $returnModel->setStatus(1);
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getInactivePlatformUserModel()
    {
        $returnModel = new Application_Model_PlatformUser();
        $returnModel->setPlatformId('プラットフォームID');
        $returnModel->setPlatformUserId('プラットフォームユーザID');
        $returnModel->setAccessToken('アクセストークン');
        $returnModel->setIdToken('IDトークン');
        $returnModel->setStatus(0);
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getBannedPlatformUserModel()
    {
        $returnModel = new Application_Model_PlatformUser();
        $returnModel->setPlatformId('プラットフォームID');
        $returnModel->setPlatformUserId('プラットフォームユーザID');
        $returnModel->setAccessToken('アクセストークン');
        $returnModel->setIdToken('IDトークン');
        $returnModel->setStatus(6);
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getUserModel()
    {
        // account作成
        $accountModel = new Application_Model_ApplicationUser();
        $accountModel->setApplicationId('appID');
        $accountModel->setApplicationUserId('アプリケーションユーザID');
        $accountModel->setCreatedDate('2013-11-11 11:11:11');
        $accountModel->setUpdatedDate('2013-11-11 11:11:11');

        $returnModel = new Application_Model_User();
        $returnModel->setUserId('ユーザID');
        $returnModel->setStatus(1);
        $returnModel->setApps(array($accountModel));
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getInactiveUserModel()
    {
        // account作成
        $accountModel = new Application_Model_ApplicationUser();
        $accountModel->setApplicationId('appID');
        $accountModel->setApplicationUserId('アプリケーションユーザID');
        $accountModel->setCreatedDate('2013-11-11 11:11:11');
        $accountModel->setUpdatedDate('2013-11-11 11:11:11');

        $returnModel = new Application_Model_User();
        $returnModel->setUserId('ユーザID');
        $returnModel->setStatus(0);
        $returnModel->setApps(array($accountModel));
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getUserPlatformApplicationRelationModel()
    {
        $returnModel = new Application_Model_UserPlatformApplicationRelation();
        $returnModel->setUserId(111);
        $returnModel->setPlatformId('プラットフォームID');
        $returnModel->setPlatformUserId('プラットフォームユーザID');
        $returnModel->setApplicationId('appID');
        $returnModel->setAccessToken('1953a7bbe447613bb1f9eca1ec3d8e290640e7ba0d07ac17277e307940993b20');
        $returnModel->setIdToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vbWlzcC5zdGFuZGFyZGl6YXRpb24ubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDgzMTEwMDAwIiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.R2VLAl4hsUKEwXxoDmXxmLaTct5vEbXyU15MNkSPAS8');
        $returnModel->setAuthorizationCode(NULL);
        $returnModel->setRefreshToken('refresh_token');
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getUserPlatformApplicationRelationModels()
    {
        $returnModel = new Application_Model_UserPlatformApplicationRelation();
        $returnModel->setUserId(111);
        $returnModel->setPlatformId('プラットフォームID');
        $returnModel->setPlatformUserId('プラットフォームユーザID');
        $returnModel->setApplicationId('appID');
        $returnModel->setAccessToken('1953a7bbe447613bb1f9eca1ec3d8e290640e7ba0d07ac17277e307940993b20');
        $returnModel->setIdToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vZGV2LW1pc3AubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDUxNTczOTk5IiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.qZu4EjtTEnUdgLlikkgBcttzkjB_-GJJb5iP8wb9C7k');
        $returnModel->setAuthorizationCode(NULL);
        $returnModel->setRefreshToken('refresh_token');
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getUserPlatformApplicationRelationModelBasic()
    {
        $returnModel = new Application_Model_UserPlatformApplicationRelation();
        $returnModel->setUserId(111);
        $returnModel->setPlatformId('プラットフォームID');
        $returnModel->setPlatformUserId('プラットフォームユーザID');
        $returnModel->setApplicationId('appID');
        $returnModel->setAccessToken('1953a7bbe447613bb1f9eca1ec3d8e290640e7ba0d07ac17277e307940993b20');
        $returnModel->setIdToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vZGV2LW1pc3AubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDUxNTczOTk5IiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.qZu4EjtTEnUdgLlikkgBcttzkjB_-GJJb5iP8wb9C7k');
        $returnModel->setAuthorizationCode('認可コード');
        $returnModel->setRefreshToken('refresh_token');
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getUserPlatformApplicationRelationModelBasicFeature()
    {
        $setDate = date("Y-m-d H:i:s", strtotime("-1 minute"));

        $returnModel = new Application_Model_UserPlatformApplicationRelation();
        $returnModel->setUserId(111);
        $returnModel->setPlatformId('プラットフォームID');
        $returnModel->setPlatformUserId('プラットフォームユーザID');
        $returnModel->setApplicationId('appID');
        $returnModel->setAccessToken('1953a7bbe447613bb1f9eca1ec3d8e290640e7ba0d07ac17277e307940993b20');
        $returnModel->setIdToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vZGV2LW1pc3AubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDUxNTczOTk5IiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.qZu4EjtTEnUdgLlikkgBcttzkjB_-GJJb5iP8wb9C7k');
        $returnModel->setAuthorizationCode('認可コード');
        $returnModel->setRefreshToken('refresh_token');
        $returnModel->setCreatedDate($setDate);
        $returnModel->setUpdatedDate($setDate);

        return $returnModel;
    }

    public function getUserPlatformApplicationRelationModelOther()
    {
        $returnModel = new Application_Model_UserPlatformApplicationRelation();
        $returnModel->setUserId(777);
        $returnModel->setPlatformId('プラットフォームID_o');
        $returnModel->setPlatformUserId('プラットフォームユーザID_o');
        $returnModel->setApplicationId('appID');
        $returnModel->setAccessToken('1953a7bbe447613bb1f9eca1ec3d8e290640e7ba0d07ac17277e307940993b20');
        $returnModel->setIdToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vZGV2LW1pc3AubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDUxNTczOTk5IiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.qZu4EjtTEnUdgLlikkgBcttzkjB_-GJJb5iP8wb9C7k');
        $returnModel->setAuthorizationCode(NULL);
        $returnModel->setRefreshToken('refresh_token');
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getErrorUserPlatformApplicationRelationModel()
    {
        $returnModel = new Application_Model_UserPlatformApplicationRelation();
        $returnModel->setUserId('ユーザID');
        $returnModel->setPlatformId('プラットフォームID');
        $returnModel->setPlatformUserId('プラットフォームユーザID');
        $returnModel->setApplicationId('appID');
        $returnModel->setAccessToken('検証に失敗するアクセストークン');
        $returnModel->setIdToken('検証に失敗するIDトークン');
        $returnModel->setRefreshToken('存在しないリフレッシュトークン');
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getArrayMultiUserPlatformApplicationRelationModel($count)
    {
        $returnArray = array();

        for ($i = 0; $i <= $count; $i++) {
            $returnModel     = new Application_Model_UserPlatformApplicationRelation();
            $returnModel->setUserId('ユーザID_' . $i);
            $returnModel->setPlatformUserId('プラットフォームユーザID_' . $i);
            $returnModel->setPlatformId('プラットフォームID_' . $i);
            $returnModel->setApplicationId('appID_' . $i);
            $returnModel->setAccessToken('アクセストークン_' . $i);
            $returnModel->setIdToken('IDトークン_' . $i);
            $returnModel->setRefreshToken('refresh_token_' . $i);
            $returnModel->setCreatedDate('2013-11-11 11:11:' . $i . $i);
            $returnModel->setUpdatedDate('2013-11-11 11:11:' . $i . $i);
            $returnArray[$i] = $returnModel;
        }

        return $returnArray;
    }

    public function getApplicationUserModel()
    {
        $returnModel = new Application_Model_ApplicationUser();
        $returnModel->setApplicationId('appID');
        $returnModel->setApplicationUserId('アプリケーションユーザID');
        $returnModel->setApplicationWorldId(' ');
        $returnModel->setApplicationUserName('アプリケーションユーザ名');
        $returnModel->setPassword('パスワード');
        $returnModel->setAccessToken('アクセストークン');
        $returnModel->setIdToken('IDトークン');
        $returnModel->setStatus(1);
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getApplicationModel()
    {
        $returnModel = new Application_Model_Application();
        $returnModel->setApplicationId('00001');
        $returnModel->setDeveloperId('デベロッパーID');
        $returnModel->setApplicationName('アプリケーション名');
        $returnModel->setApplicationSecret('KvU4kb8bmVbwYfzwj4vNlItjlwnBniRQ050gSbo7z8lcnRAqjAmrdLqDP4eHhgVF');
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getApplicationUserPlatformRelationModel()
    {
        $returnModel = new Application_Model_ApplicationUserPlatformRelation();
        $returnModel->setApplicationId('appID');
        $returnModel->setApplicationUserId('アプリケーションユーザID');
        $returnModel->setApplicationWorldId('アプリケーションワールドID');
        $returnModel->setPlatformId('プラットフォームID');
        $returnModel->setPlatformUserId('プラットフォームユーザID');
        $returnModel->setCreatedDate('2013-11-11 11:11:11');
        $returnModel->setUpdatedDate('2013-11-11 11:11:11');

        return $returnModel;
    }

    public function getArrayMultiApplicationUserPlatformRelationModel($count)
    {
        $returnArray = array();

        for ($i = 0; $i < $count; $i++) {
            $returnModel     = new Application_Model_ApplicationUserPlatformRelation();
            $returnModel->setPlatformUserId('プラットフォームユーザID_' . $i);
            $returnModel->setPlatformId('プラットフォームID_' . $i);
            $returnModel->setApplicationId('appID_' . $i);
            $returnModel->setApplicationUserId('アプリケーションユーザID_' . $i);
            $returnModel->setCreatedDate('2013-11-11 11:11:' . $i . $i);
            $returnModel->setUpdatedDate('2013-11-11 11:11:' . $i . $i);
            $returnArray[$i] = $returnModel;
        }

        return $returnArray;
    }

    public function getApplicationRedirectUriModel()
    {
        $returnModel = new Application_Model_ApplicationRedirectUri();
        $returnModel->setApplicationId('00001');
        $returnModel->setRedirectUri('https://redirect.com');
        $returnModel->setCreatedDate('3013-11-11 11:11:11');
        $returnModel->setUpdatedDate('3013-11-11 11:11:11');

        return $returnModel;
    }

    public function getBuildParam()
    {
        return array('aaa' => 'bbb');
    }

    public function getTokenModel()
    {
        return new Application_Model_CommonExternalPlatformToken();
    }

    public function getPeopleModel()
    {
        return new Application_Model_CommonExternalPlatformPeople();
    }

    /**
     * @Given /^モック "([^"]*)" の "([^"]*)" で (\d+) が返ってくる処理がセットされる$/
     */
    public function biLiahogefugapoo($arg1, $arg2, $arg3)
    {
        // モックロジック取得
        $context   = $this->getMainContext();
        $mock      = $context->getParam($arg1 . 'Mock');
        $mockClass = $context->getParam($arg1);

        // メソッド毎の設定
        $mockClass->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($arg3));

        $context->setParam($arg1 . 'Mock', $mock);
        $context->setParam($arg1, $mockClass);
    }

}
