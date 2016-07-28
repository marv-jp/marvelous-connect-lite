<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//
// PHPUnitの読み込み
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';
require_once realpath(dirname(__FILE__) . '/../../tests/bootstrap.php');

require_once realpath(dirname(__FILE__) . '/../../features/Mock/ApiMode.php');
require_once realpath(dirname(__FILE__) . '/../../features/BehatMock.php');

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    // 引数
    private $_args;
    private $_argsModel;
    private $_value = array();
    // モデル
    protected $_model;   //1つ目の引数
    protected $_collectionModel; // _model用
    protected $_model2;  //2つ目の引数
    protected $_model3;  //3つ目の引数
    // マッパー
    protected $_applicationMapper;
    protected $_applicationUserMapper;
    protected $_applicationUserMapperInsert;
    protected $_applicationUserMapperUpdate;
    protected $_platformMapper;
    protected $_platformUserMapper;
    protected $_userMapper;
    protected $_userPlatformApplicationRelationMapper;
    protected $_applicationUserPlatformRelationMapper;
    protected $_applicationRedirectUriMapper;
    //ロジック
    protected $_logicApplication;
    protected $_logicApplicationUser;
    protected $_logicUser;
    protected $_logicCommonOidcToken;
    protected $_apiMode;
    // 各モック用のBehatMock
    protected $_applicationMapperMock;
    protected $_applicationUserMapperMock;
    protected $_platformMapperMock;
    protected $_platformUserMapperMock;
    protected $_userMapperMock;
    protected $_userPlatformApplicationRelationMapperMock;
    protected $_applicationUserPlatformRelationMapperMock;
    protected $_applicationRedirectUriMapperMock;
    protected $_logicApplicationMock;
    protected $_logicApplicationUserMock;
    protected $_logicUserMock;
    protected $_logicCommonOidcTokenMock;
    protected $_apiModeMock;
    protected $_mapper;
    protected $_logic;
    protected $_mapperMock;
    protected $_logicMock;
    // モック用データ
    protected $_mockData;
    // ロジックの返り値
    protected $_return;
    protected $_result;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $paths    = array(APPLICATION_PATH);
        $basePath = implode(DIRECTORY_SEPARATOR, $paths);

        // Zendのルールから外れるものを登録する。
        // 注意：ディレクトリ名の先頭は大文字にする
        $autoLoader     = Zend_Loader_Autoloader::getInstance();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array('basePath' => $basePath, 'namespace' => ''));
        $resourceLoader->addResourceType('application_model', 'models/', 'Application_Model_');
        $resourceLoader->addResourceType('logics', 'logics/', 'Logic_');
        $resourceLoader->addResourceType('common', 'common/', 'Common_');
        $resourceLoader->addResourceType('validate', 'validate/', 'Validate_');
        $resourceLoader->addResourceType('misp', 'misp/', 'Misp_');

        // Common_Dbのトランザクションの処理をモック化
        $ref = new ReflectionProperty('Common_Db', '_transactionLevel');
        $ref->setAccessible(true);
        $ref->setValue(null, array('mainDb' => 0, 'subDb' => 0));

        // BehatMock#setUpでZendのブートストラップが起動する前にDBをモック化するために、
        // application.iniを読み込んでDB設定を取得する
        $applicationIni = APPLICATION_PATH . '/configs/application.ini';
        $configIni      = new Zend_Config_Ini($applicationIni, APPLICATION_ENV);
        $config         = $configIni->toArray();
        $dbConfig       = $config['db'];
        $mispConfig     = $config['misp'];

        $this->_httpMock = new Common_Http_Mock();

        // DBモック化
        $mock            = new BehatMock();
        $commonDbAdapter = $mock->getMock('Common_Db_Adapter_Pdo_Mysql', array('commit', 'rollBack', 'beginTransaction', '_connect', 'quote', 'prepare', 'execute', 'query', 'rowCount', 'delete'), array($dbConfig[$mispConfig['db']['main']], $dbConfig[$mispConfig['db']['sub']]));

        // (モック)DBを各実装クラス内から呼び出せるようZendレジストリに登録
        Zend_Registry::set($mispConfig['db']['main'], $commonDbAdapter);
        Zend_Registry::set($mispConfig['db']['sub'], $commonDbAdapter);

        // ここでようやくブートストラップ起動
        $mock->setUp();

        // -------------------------サブコンテキスト
        $this->useContext('Aplication_alias', new ApplicationContext(array(
        )));
        $this->useContext('Mock_alias', new MockContext(array(
        )));
        $this->useContext('ApplicationUser_alias', new ApplicationUserContext(array(
        )));
        $this->useContext('User_alias', new UserContext(array(
        )));
        $this->useContext('Execute_alias', new Common_ExecuteContext(array(
        )));
        $this->useContext('Confirmation_alias', new Common_ConfirmationContext(array(
        )));
        $this->useContext('Preparation_alias', new Common_PreparationContext(array(
        )));
    }

    /**
     * メソッドの実行結果をセットします
     *
     * @param mixed $return メソッドの実行結果
     */
    public function setReturn($return)
    {
        $this->_return = $return;
    }

    /**
     * 指定されたキーのパラメータを返します
     *
     * @param string $paramKey パラメータのキー名
     * @return mixed 指定されたキーのパラメータ
     */
    public function getParam($paramKey)
    {
        $paramName = '_' . $paramKey;
        return $this->$paramName;
    }

    /**
     * 指定された配列要素番号に対応する引数値を返します
     *
     * @param integer $argNum featureファイルに記述した「引数n～」のnの部分
     * @return mixed 指定された配列要素番号に対応する引数値
     */
    public function getArg($argNum)
    {
        return $this->_args[$argNum];
    }

    /**
     * 指定された配列要素番号に対応する引数値を返します
     *
     * @param integer $argNum featureファイルに記述した「引数n～」のnの部分
     * @return mixed 指定された配列要素番号に対応する引数値
     */
    public function getArgModel($argNum)
    {
        return $this->_argsModel[$argNum];
    }

    /**
     * 指定されたキーのパラメータをセットします
     *
     * @param string $paramKey パラメータのキー名
     * @param mixed $paramValue パラメータの値
     */
    public function setParam($paramKey, $paramValue)
    {
        $paramName        = '_' . $paramKey;
        $this->$paramName = $paramValue;
    }

    /**
     * @Given /^プロパティ "([^"]*)" は (\d+) 文字を超える文字列長の値になる$/
     */
    public function yun($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        $overStr   = str_repeat("a", $arg2 + 1);
        $this->_model->$setMethod($overStr);
    }

    /**
     * @Given /^コレクションに使用されるモデルのプロパティ "([^"]*)" は (\d+) 文字を超える文字列長の値になる$/
     */
    public function zhiYun($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        $overStr   = str_repeat("a", $arg2 + 1);
        $this->_collectionModel->$setMethod($overStr);
    }

    /**
     * @Given /^引数には "([^"]*)" モデルを使用する$/
     */
    public function zhi($arg1)
    {
        if (class_exists($arg1)) {
            $this->_model = new $arg1();
        } else {
            BehatMock::fail("クラスねーわw");
        }
    }

    /**
     * @Given /^引数には "([^"]*)" の "([^"]*)" を使用する$/
     */
    public function zhi6($arg1, $arg2)
    {
        if (class_exists($arg1)) {
            $fixtures         = new $arg1();
            $fixturesProperty = 'get' . $arg2 . 'Set';

            $this->_model = $fixtures->$fixturesProperty;
        } else {
            BehatMock::fail("クラスねーわw");
        }
    }

    /**
     * @Given /^ロジックへのセット項目(\d+)として "([^"]*)" の "([^"]*)" を使用する$/

     */
    public function liaZhi($arg1, $arg2, $arg3)
    {
        if (class_exists($arg2)) {
            $fixtures         = new $arg2();
            $fixturesProperty = 'get' . $arg3 . 'Set';

            $this->_argsModel[$arg1] = $fixtures->$fixturesProperty;
        } else {
            BehatMock::fail("クラスねーわw");
        }
    }

    /**
     * @Given /^引数には "([^"]*)" コレクションを使用する$/
     */
    public function zhi3($arg1)
    {
        if (class_exists($arg1)) {
            $this->_model = new $arg1();
        } else {
            BehatMock::fail("クラスねーわw");
        }
    }

    /**
     * @Given /^プロパティ "([^"]*)" には "([^"]*)" がセットされている$/
     */
    public function stepDefinition1($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        if (method_exists($this->_model, $setMethod)) {
            $this->_model->$setMethod($arg2);
        } else {
            BehatMock::fail($setMethod . "ねーわ");
        }
    }

    /**
     * @Given /^引数のコレクションには "([^"]*)" モデルが必要$/
     */
    public function yi($arg1)
    {
        if (class_exists($arg1)) {
            $this->_collectionModel = new $arg1();
        } else {
            BehatMock::fail("クラスねーわw");
        }
    }

    /**
     * @Given /^コレクションに使用されるモデルのプロパティ "([^"]*)" には "([^"]*)" がセットされている$/
     */
    public function zhi4($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        if (method_exists($this->_collectionModel, $setMethod)) {
            $this->_collectionModel->$setMethod($arg2);
        } else {
            BehatMock::fail($setMethod . "ねーわ");
        }
    }

    /**
     * @Given /^コレクションに使用されるモデルのプロパティ "([^"]*)" には (\d+) がセットされている$/
     */
    public function zhi5($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        if (method_exists($this->_collectionModel, $setMethod)) {
            $this->_collectionModel->$setMethod($arg2);
        } else {
            BehatMock::fail($setMethod . "ねーわ");
        }
    }

    /**
     * @Given /^引数のコレクションにはモデルがアタッチされる$/
     */
    public function v2()
    {
        $this->_model->attach($this->_collectionModel);
        $this->_model->rewind();
    }

    /**
     * @Given /^プロパティ "([^"]*)" には (\d+) がセットされている$/
     */
    public function stepDefinition7($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        if (method_exists($this->_model, $setMethod)) {
            $this->_model->$setMethod($arg2);
        } else {
            BehatMock::fail($setMethod . "ねーわ");
        }
    }

    /**
     * @Given /^引数２つ目のプロパティ "([^"]*)" には (\d+) がセットされている$/
     */
    public function daebsJu2($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        if (method_exists($this->_model2, $setMethod)) {
            $this->_model2->$setMethod($arg2);
        } else {
            BehatMock::fail($setMethod . "ねーわ");
        }
    }

    /**
     * @Given /^プロパティ "([^"]*)" にはNULLがセットされている$/
     */
    public function nullllllewar($arg1)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        if (method_exists($this->_model, $setMethod)) {
            $this->_model->$setMethod(NULL);
        } else {
            BehatMock::fail($setMethod . "ねーわ");
        }
    }

    /**
     * @Given /^引数２つ目には "([^"]*)" モデルを使用する$/
     */
    public function daebsJuZhi($arg1)
    {
        $this->_model2 = new $arg1();
    }

    /**
     * @Given /^引数２つ目のプロパティ "([^"]*)" には "([^"]*)" がセットされている$/
     */
    public function daebsJu($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        $this->_model2->$setMethod($arg2);
    }

    /**
     * @Given /^引数３つ目には "([^"]*)" モデルを使用する$/
     */
    public function daesJuZhi($arg1)
    {
        $this->_model3 = new $arg1();
    }

    /**
     * @Given /^引数３つ目のプロパティ "([^"]*)" には "([^"]*)" がセットされている$/
     */
    public function daesJu($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        $this->_model3->$setMethod($arg2);
    }

    /**
     * @Given /^引数２つ目のプロパティ "([^"]*)" は (\d+) 文字を超える文字列長の値になる$/
     */
    public function daebsJuYun($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        $overStr   = str_repeat("a", $arg2 + 1);
        $this->_model2->$setMethod($overStr);
    }

    /**
     * @Given /^引数３つ目のプロパティ "([^"]*)" は (\d+) 文字を超える文字列長の値になる$/
     */
    public function daesJuYun($arg1, $arg2)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        $overStr   = str_repeat("a", $arg2 + 1);
        $this->_model3->$setMethod($overStr);
    }

    /**
     * @Given /^モックにアプリケーションユーザがヒットする処理をセットする$/
     */
    public function lia()
    {
        $mock                         = new BehatMock();
        $this->_applicationUserMapper = $mock->getMock('Application_Model_ApplicationUserMapper');
        $this->_applicationUserMapper
                ->expects($mock->any())
                ->method('find')
                ->will($mock->returnValue(new Application_Model_ApplicationUser()));
    }

    /**
     * @Given /^"既に登録対象が存在しています"の例外が返ってくること$/
     */
    public function fouZhangNaoNanoBi(PyStringNode $string)
    {
        $className = get_class($this->_return);
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^"対象が存在しません"の例外が返ってくること$/
     */
    public function naoNanoBi(PyStringNode $string)
    {
        $className = get_class($this->_return);
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^例外が発生しないこと$/
     */
    public function noREIGAI()
    {
        $className = get_class($this->_return);
        if ($this->_return instanceof Exception) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^"([^"]*)" プロパティと "([^"]*)" プロパティに異なる値が入っていること$/
     */
    public function lan($arg1, $arg2)
    {
        $getMethod1 = 'get' . Common_Util_String::snakeToCamel($arg1);
        $getMethod2 = 'get' . Common_Util_String::snakeToCamel($arg2);

        if ($this->_return->$getMethod1() == $this->_return->$getMethod2()) {
            throw new Exception();
        }
    }

    /**
     * @Given /^"([^"]*)" プロパティに値が入っていること$/
     */
    public function stepDefinition2($arg1)
    {
        $getMethod   = 'get' . Common_Util_String::snakeToCamel($arg1);
        $getProperty = $this->_return->$getMethod();

        if (strlen($getProperty) == 0) {
            throw new Exception();
        }
    }

    /**
     * @Given /^"([^"]*)" プロパティに "([^"]*)" 以外の値が入っていること$/
     */
    public function stepDefinition6($arg1, $arg2)
    {
        $getMethod   = 'get' . Common_Util_String::snakeToCamel($arg1);
        $getProperty = $this->_return->$getMethod();

        if ($getProperty == $arg2) {
            throw new Exception();
        }
    }

    /**
     * @Given /^"([^"]*)" プロパティがNULLであること$/
     */
    public function nullnull($arg1)
    {
        $getMethod   = 'get' . Common_Util_String::snakeToCamel($arg1);
        $getProperty = $this->_return->$getMethod();

        if (!is_null($getProperty)) {
            throw new Exception();
        }
    }

    /**
     * @Given /^"([^"]*)" モデルが返されること$/
     */
    public function bi2($arg1)
    {
        $mock = new BehatMock();
        $mock->assertEquals(get_class($this->_return), $arg1);
    }

    /**
     * @Given /^"([^"]*)" コレクションが返されること$/
     */
    public function bi4($arg1)
    {
        $mock = new BehatMock();
        $mock->assertEquals(get_class($this->_return), $arg1);
    }

    /**
     * @Given /^コレクションには "([^"]*)" モデルがアタッチされていること$/
     */
    public function v3($arg1)
    {
        $this->_return->rewind();
        $returnModel = $this->_return->current();
        $mock        = new BehatMock();
        $mock->assertEquals(get_class($returnModel), $arg1);
    }

    /**
     * @Given /^コレクションのポインタを進める$/
     */
    public function vYun()
    {
        $this->_return->next();
    }

    /**
     * @Given /^アタッチされていたモデルの "([^"]*)" プロパティに "([^"]*)" が入っていること$/
     */
    public function v4($arg1, $arg2)
    {
        $returnModel = $this->_return->current();
        $getMethod   = 'get' . Common_Util_String::snakeToCamel($arg1);
        $mock        = new BehatMock();
        $mock->assertEquals($returnModel->$getMethod(), $arg2);
    }

    /**
     * @Given /^"([^"]*)" プロパティに "([^"]*)" が入っていること$/
     */
    public function stepDefinition4($arg1, $arg2)
    {
        $getMethod = 'get' . Common_Util_String::snakeToCamel($arg1);
        $mock      = new BehatMock();
        $mock->assertEquals($this->_return->$getMethod(), $arg2);
    }

    /**
     * @Given /^"([^"]*)" が返ってくること$/
     */
    public function bi($arg1)
    {
        $mock = new BehatMock();
        $mock->assertEquals($this->_return, $arg1);
    }

    /**
     * @Given /^"true"の結果が返ってくること$/
     */
    public function trueHuanBi()
    {
        $mock = new BehatMock();
        $mock->assertTrue($this->_return);
    }

    /**
     * @Given /^"false"の結果が返ってくること$/
     */
    public function falseHuanBi()
    {
        $mock = new BehatMock();
        $mock->assertTrue(!$this->_return);
    }

    /**
     * @Given /^"認証失敗"の例外が返ってくること$/
     */
    public function heZhouBi(PyStringNode $string)
    {
        $className = get_class($this->_return);
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^"パラメータ不正"の例外が返ってくること$/
     */
    public function vBi(PyStringNode $string)
    {
        $className = get_class($this->_return);
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^"登録に失敗しました"の例外が返ってくること$/
     */
    public function fouZhangBi(PyStringNode $string)
    {
        $className = get_class($this->_return);
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^"更新が行われませんでした"の例外が返ってくること$/
     */
    public function chanBi(PyStringNode $string)
    {
        $className = get_class($this->_return);
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^"([^"]*)" プロパティと "([^"]*)" プロパティに同じ値が入っていること$/
     */
    public function stepDefinition3($arg1, $arg2)
    {
        $getMethod1 = 'get' . Common_Util_String::snakeToCamel($arg1);
        $getMethod2 = 'get' . Common_Util_String::snakeToCamel($arg2);

        if ($this->_return->$getMethod1() != $this->_return->$getMethod2()) {
            throw new Exception();
        }
    }

    /**
     * @Given /^: 引数(\d+)は "([^"]*)" 型の "([^"]*)" である$/
     */
    public function nf($arg1, $arg2, $arg3)
    {
        // 未定義の引数は新たにNULL値で宣言する
        if (!isset($this->_args[$arg1])) {
            $this->_args[$arg1] = NULL;
        }

        // ステップ文の型情報がクラスの場合、インスタンスを生成する
        $type = $this->_settype($this->_args[$arg1], $arg2);

        // ステップ文の型情報に応じて値をセットする
        // array指定は空のarrayをセットする
        // オブジェクトの場合はインスタンスだけ生成してセットする
        // それ以外は渡された値をそのままセットする
        switch ($type) {
            case "array":
                $this->_args[$arg1] = array();
                break;
            case "object":
                $this->_args[$arg1] = new $arg2();
                break;
            default:
                $this->_args[$arg1] = $arg3;
                break;
        }
    }

    /**
     * @Given /^: 引数(\d+)のプロパティ "([^"]*)" に "([^"]*)" 型の "([^"]*)" がセットされている$/
     */
    public function nf2($arg1, $arg2, $arg3, $arg4)
    {
        $argNum   = $arg1;
        $property = $arg2;
        $type     = $arg3;
        $value    = $arg4;

        $setMethod = 'set' . ucfirst(Common_Util_String::snakeToCamel($property));

        // 指定された型情報をセットする
        settype($value, $type);

        // 値をセットする
        $this->_args[$argNum]->$setMethod($value);
    }

    /**
     * @Given /^: "([^"]*)" 型の "([^"]*)" が返却されること$/
     */
    public function nfBi($arg1, $arg2)
    {
        $expectedType  = $arg1;
        $expectedValue = $arg2;
        $actualType    = gettype($this->_return);

        if ('object' == strtolower($actualType)) {
            $actualType = get_class($this->_return);
        } elseif ('boolean' == $actualType) {
            if ('true' == $expectedValue) {
                $expectedValue = TRUE;
            } else {
                $expectedValue = FALSE;
            }
        }
        $mock = new BehatMock();

        $mock->assertEquals($expectedType, $actualType);
        $mock->assertEquals($expectedValue, $this->_return);
    }

    /**
     * @Given /^"([^"]*)" 型の "([^"]*)" が返却されること$/
     */
    public function nfBi2($arg1, $arg2)
    {
        switch ($arg1) {
            case "boolean":
                if ((boolean) $this->_return != (boolean) $arg2) {
                    throw new Exception();
                }
                break;
            default:
                throw new Exception();
        }
    }

    /**
     * @Given /^"パラメータ不正"の例外が発生すること$/
     */
    public function vLiChang(PyStringNode $string)
    {
        $className = get_class($this->_return);
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^: 引数(\d+)のプロパティ "([^"]*)" に (\d+) 文字を超える値がセットされている$/
     */
    public function yun2($arg1, $arg2, $arg3)
    {
        $argNum   = $arg1;
        $property = $arg2;
        $length   = $arg3;

        $setMethod = 'set' . ucfirst(Common_Util_String::snakeToCamel($property));
        $this->_args[$argNum]->$setMethod(str_repeat('a', $length + 1));
    }

    /**
     * @Given /^"([^"]*)" プロパティに (\d+) が入っていること$/
     */
    public function stepDefinition5($arg1, $arg2)
    {
        $getMethod = Common_Util_String::snakeToCamel('get_' . $arg1);
        $mock      = new BehatMock();
        $mock->assertEquals($this->_return->$getMethod(), $arg2);
    }

    /**
     * @Given /^"([^"]*)" の例外が返ってくること$/
     */
    public function bi3($arg1, PyStringNode $string)
    {
        $className = get_class($this->_return);
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^プロパティ "([^"]*)" は空である$/
     */
    public function sin($arg1)
    {
        //何もしない事をする
    }

    /**
     * @Given /^引数２つ目のプロパティ "([^"]*)" は空である$/
     */
    public function daebsJuSin($arg1)
    {
        //何もしない事をする
    }

    /**
     * @Given /^"([^"]*)" プロパティに配列が入っていること$/
     */
    public function kuai2($arg1)
    {
        $getMethod = Common_Util_String::snakeToCamel('get_' . $arg1);
        $mock      = new BehatMock();
        $mock->assertTrue(is_array($this->_return->$getMethod()));
    }

    /**
     * @Given /^"([^"]*)" プロパティの配列\[(\d+)\]は "([^"]*)" モデルが入っていること$/
     */
    public function kuai3($arg1, $arg2, $arg3)
    {
        $getMethod = Common_Util_String::snakeToCamel('get_' . $arg1);
        $array     = $this->_return->$getMethod();
        $mock      = new BehatMock();
        $mock->assertEquals($arg3, get_class($array[$arg2]));
    }

    /**
     * @Given /^"([^"]*)" プロパティの配列\[(\d+)\]に入っているモデルの "([^"]*)" プロパティに "([^"]*)" が入っていること$/
     */
    public function kuai4($arg1, $arg2, $arg3, $arg4)
    {
        $getModelMethod = Common_Util_String::snakeToCamel('get_' . $arg1);
        $array          = $this->_return->$getModelMethod();
        $model          = $array[$arg2];
        $getMethod      = Common_Util_String::snakeToCamel('get_' . $arg3);
        $mock           = new BehatMock();
        $mock->assertEquals($arg4, $model->$getMethod());
    }

    /**
     * @Given /^配列が返されること$/
     */
    public function kuaiBi()
    {
        if (!is_array($this->_return)) {
            throw new Exception();
        }
    }

    /**
     * @Given /^配列\[(\d+)\]の中身に "([^"]*)" モデルが入っていること$/
     */
    public function kuaiCha($arg1, $arg2)
    {
        $mock = new BehatMock();
        $mock->assertEquals(get_class($this->_return[$arg1]), $arg2);
    }

    /**
     * @Given /^配列\[(\d+)\]内モデルの "([^"]*)" プロパティに "([^"]*)" が入っていること$/
     */
    public function kuai($arg1, $arg2, $arg3)
    {
        $getMethod = Common_Util_String::snakeToCamel('get_' . $arg2);

        $mock        = new BehatMock();
        $returnModel = $this->_return[$arg1];

        $mock->assertEquals($returnModel->$getMethod(), $arg3);
    }

    /**
     * @Given /^配列\[(\d+)\]内モデルの "([^"]*)" プロパティに "([^"]*)" 以外の値が入っていること$/
     */
    public function kuai6($arg1, $arg2, $arg3)
    {
        $getMethod = Common_Util_String::snakeToCamel('get_' . $arg2);

        $returnModel = $this->_return[$arg1];

        if ($returnModel->$getMethod() == $arg3) {
            throw new Exception;
        }
    }

    /**
     * @Given /^配列\[(\d+)\]内モデルの "([^"]*)" プロパティと "([^"]*)" プロパティに同じ値が入っていること$/
     */
    public function kuai7($arg1, $arg2, $arg3)
    {
        $getMethod1 = Common_Util_String::snakeToCamel('get_' . $arg2);
        $getMethod2 = Common_Util_String::snakeToCamel('get_' . $arg3);

        $returnModel = $this->_return[$arg1];

        if ($returnModel->$getMethod1() != $returnModel->$getMethod2()) {
            throw new Exception;
        }
    }

    /**
     * @Given /^引数には "([^"]*)" モデルの (\d+) 行配列を使用する（モデルの中身は "([^"]*)" ）$/
     */
    public function chanKuaiZhiDaelChaDaelg($arg1, $arg2, $arg3)
    {
        $setArray = array();
        $context  = $this->getSubcontext('Mock_alias');

        for ($i = 0; $i < $arg2; $i++) {
            $setArray[] = $context->$arg3();
        }

        $this->_model = $setArray;
    }

    /**
     * @Given /^配列\[(\d+)\]内モデルの "([^"]*)" プロパティがNULLであること$/
     */
    public function kuaiNull($arg1, $arg2)
    {
        $getMethod   = Common_Util_String::snakeToCamel('get_' . $arg2);
        $returnModel = $this->_return[$arg1];

        $getProperty = $returnModel->$getMethod();

        if (!is_null($getProperty)) {
            throw new Exception();
        }
    }

    /**
     * @Given /^: "([^"]*)" 型のオブジェクトが返却されること$/
     */
    public function nfBi3($arg1)
    {
        $expectedObjectType = $arg1;
        $actual             = $this->_return;

        $mock = new BehatMock();
        $mock->assertEquals($expectedObjectType, get_class($actual));
    }

    /**
     * @Given /^: "([^"]*)" クラスのオブジェクトが (\d+) つ配列で返却されること$/
     */
    public function kuaiBi2($arg1, $arg2)
    {
        $expectedObjectName = $arg1;
        $expectedCount      = $arg2;

        $mock = new BehatMock();
        // 配列であること
        $mock->assertTrue(is_array($this->_return));
        // 配列の要素数が期待値であること
        $mock->assertCount($expectedCount, $this->_return);
        // 要素が期待値のオブジェクトであること
        foreach ($this->_return as $expectedObject) {
            $mock->assertEquals($expectedObjectName, get_class($expectedObject));
        }
    }

    /**
     * @Given /^"NotFound"の例外が発生すること$/
     */
    public function notfoundLiChang(PyStringNode $string)
    {
        $className = get_class($this->_return);
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^"NotModified"の例外が発生すること$/
     */
    public function notmodifiedLiChang(PyStringNode $string)
    {
        $className = get_class($this->_return);
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^配列\[(\d+)\]内モデルの "([^"]*)" プロパティに値が入っていること$/
     */
    public function kuai5($arg1, $arg2)
    {
        $getMethod   = 'get' . Common_Util_String::snakeToCamel($arg2);
        $returnModel = $this->_return[$arg1];

        $getProperty = $returnModel->$getMethod();

        if (strlen($getProperty) == 0) {
            throw new Exception();
        }
    }

    private function _settype(&$var, $type)
    {
        // settypeが失敗したら型情報がクラスの可能性が高い
        if (!@settype($var, $type)) {
            if (class_exists($type)) {
                return 'object';
            }
        }
        return gettype($var);
    }

    /**
     * @Given /^プロパティ "([^"]*)" には (\d+) 文字列長のデータがセットされている$/
     */
    public function v($arg1, $arg2)
    {
        $setString = str_repeat("b", $arg2);

        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg1);
        if (method_exists($this->_model, $setMethod)) {
            $this->_model->$setMethod($setString);
        } else {
            BehatMock::fail($setMethod . "ねーわ");
        }
    }

    /**
     * @Given /^引数には "([^"]*)" 文字列を使用する$/
     */
    public function zhi2($arg1)
    {
        $this->_model = $arg1;
    }

    /**
     * @Given /^引数には連想配列を使用する$/
     */
    public function qiKuaiZhi()
    {
        $this->_model = array();
    }

    /**
     * @Given /^連想配列のキー "([^"]*)" には "([^"]*)" がセットされている$/
     */
    public function qiKuai($arg1, $arg2)
    {
        $this->_model[$arg1] = $arg2;
    }

    /**
     * @Given /^引数(\d+)つ目には "([^"]*)" モデルを使用する$/
     */
    public function juZhi($arg1, $arg2)
    {
        if (class_exists($arg2)) {
            $this->_args[$arg1] = new $arg2();
        } else {
            BehatMock::fail("クラスねーわw");
        }
    }

    /**
     * @Given /^引数(\d+)つ目のプロパティ "([^"]*)" には "([^"]*)" がセットされている$/
     */
    public function ju($arg1, $arg2, $arg3)
    {

        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg2);
        if (method_exists($this->_args[$arg1], $setMethod)) {
            $this->_args[$arg1]->$setMethod($arg3);
        } else {
            BehatMock::fail($setMethod . "ねーわ");
        }
    }

    /**
     * @Given /^引数(\d+)つ目には "([^"]*)" コレクションを使用する$/
     */
    public function juZhi2($arg1, $arg2)
    {
        if (class_exists($arg2)) {
            $this->_args[$arg1] = new $arg2();
        } else {
            BehatMock::fail("コレクションねーわw");
        }
    }

    /**
     * @Given /^引数(\d+)つ目のコレクションには "([^"]*)" モデルが必要$/
     */
    public function juYi($arg1, $arg2)
    {
        if (class_exists($arg2)) {
            $this->_collectionModel[$arg1] = new $arg2();
        } else {
            BehatMock::fail("クラスねーわw");
        }
    }

    /**
     * @Given /^引数(\d+)つ目のコレクションに使用されるモデルのプロパティ "([^"]*)" には "([^"]*)" がセットされている$/
     */
    public function juZhi3($arg1, $arg2, $arg3)
    {
        $setMethod = 'set' . Common_Util_String::snakeToCamel($arg2);
        if (method_exists($this->_collectionModel[$arg1], $setMethod)) {
            $this->_collectionModel[$arg1]->$setMethod($arg3);
        } else {
            BehatMock::fail($setMethod . "ねーわ");
        }
    }

    /**
     * @Given /^引数(\d+)つ目のコレクションにはモデルがアタッチされる$/
     */
    public function juV($arg1)
    {
        $this->_args[$arg1]->attach($this->_collectionModel[$arg1]);
        $this->_args[$arg1]->rewind();
    }

    /**
     * @Given /^引数(\d+)つ目には連想配列を使用する$/
     */
    public function daessJuQiKuaiZhi($index)
    {
        $this->_args[$index] = array();
    }

    /**
     * @Given /^引数(\d+)つ目の連想配列のキー "([^"]*)" の値は "([^"]*)" がセットされている$/
     */
    public function daessJuQiKuai($index, $keyName, $value)
    {
        $this->_args[$index] = array_merge($this->_args[$index], array($keyName => $value));
    }

    /**
     * @Given /^引数の "([^"]*)" キーの "([^"]*)" キーを削除$/
     */
    public function mang($arg1, $arg2)
    {
        unset($this->_model[$arg1][$arg2]);
    }

    /**
     * @Given /^引数の "([^"]*)" キーの (\d+) 番目配列の "([^"]*)" キーを削除$/
     */
    public function mankkkg($arg1, $arg2, $arg3)
    {
        unset($this->_model[$arg1][$arg2][$arg3]);
    }

    /**
     * @Given /^引数の \"([^\"]*)\" キーの \"([^\"]*)\" キーの (\d+) 番目配列の \"([^\"]*)\" キーを削除$/
     */
    public function yuJuKuaiMang($arg1, $arg2, $arg3, $arg4)
    {
        unset($this->_model[$arg1][$arg2][$arg3][$arg4]);
    }

    /**
     * @Given /^引数の "([^"]*)" キーの (\d+) 番目配列の "([^"]*)" キーの (\d+) 番目配列の "([^"]*)" キーを削除$/
     */
    public function yuJuKuaiYuJuKuaiMang($arg1, $arg2, $arg3, $arg4, $arg5)
    {
        unset($this->_model[$arg1][$arg2][$arg3][$arg4][$arg5]);
    }

    /**
     * @Given /^メソッド "([^"]*)" で "([^"]*)" モデルが取得できる$/
     */
    public function v5($arg1, $arg2)
    {
        $mock   = new BehatMock();
        $return = $this->_return;

        $m = $return->$arg1();
        $mock->assertEquals($arg2, get_class($m));
    }

    /**
     * @Given /^メソッド "([^"]*)" で配列が取得できる$/
     */
    public function vKuai($arg1)
    {
        $mock   = new BehatMock();
        $return = $this->_return;
        $array  = $return->$arg1();
        $mock->assertTrue(is_array($array));
    }

    /**
     * @Given /^メソッド "([^"]*)" で取得した値に対し以下の確認を行う$/
     */
    public function vXuanHeChan($arg1)
    {
        $return        = $this->_return;
        $this->_result = $return->$arg1();
    }

    /**
     * @Given /^キー "([^"]*)" "([^"]*)" "([^"]*)" の値が "([^"]*)" である$/
     */
    public function stepDefinition8($arg1, $arg2, $arg3, $arg4)
    {
        $mock  = new BehatMock();
        $array = $this->_result;
        $mock->assertEquals($arg4, $array[$arg1][$arg2][$arg3]);
    }

    /**
     * @Given /^キー "([^"]*)" "([^"]*)" の値が "([^"]*)" である$/
     */
    public function stepDefinition9($arg1, $arg2, $arg3)
    {
        $mock  = new BehatMock();
        $array = $this->_result;
        $mock->assertEquals($arg3, $array[$arg1][$arg2]);
    }

    /**
     * @Given /^キー "([^"]*)" "([^"]*)" (\d+) "([^"]*)" の値が "([^"]*)" である$/
     */
    public function stepDefinition10($arg1, $arg2, $arg3, $arg4, $arg5)
    {
        $mock  = new BehatMock();
        $array = $this->_result;
        $mock->assertEquals($arg5, $array[$arg1][$arg2][$arg3][$arg4][$arg5]);
    }

    /**
     * @Given /^引数には "([^"]*)" の "([^"]*)" の "([^"]*)" の (\d+) 番目を使用する$/
     */
    public function yuJuZhi($arg1, $arg2, $arg3, $arg4)
    {
        if (class_exists($arg1)) {
            $fixtures         = new $arg1();
            $fixturesProperty = 'get' . $arg2 . 'Set';

            $arrayValue = $fixtures->$fixturesProperty;

            $this->_model = $arrayValue[$arg3][$arg4];
        } else {
            BehatMock::fail("クラスねーわw");
        }
    }

    /**
     * @Given /^メソッド "([^"]*)" で空の配列が取得できる$/
     */
    public function vSinKuai($arg1)
    {
        $mock   = new BehatMock();
        $return = $this->_return;
        $array  = $return->$arg1();
        $mock->assertTrue(!$array);
    }

    /**
     * @Given /^引数(\d+)には "([^"]*)" 文字列を使用する$/
     */
    public function zhi7Recovery($arg1, $arg2)
    {
        $this->_args[$arg1] = $arg2;
    }

    /**
     * 引数をセットします。
     * 
     * @param string $name 引数キー
     * @param mixed $value 引数の値 
     */
    public function setValue($name, $value)
    {
        $this->_value[$name] = $value;
    }

    /**
     * セットした引数の値を返します。
     * 
     * @param string $name セットした引数キー
     * @return mixed 引数の値
     * @throws Exception 要求した引数が存在しない
     */
    public function getValue($name)
    {
        if (array_key_exists($name, $this->_value)) {
            return $this->_value[$name];
        }

        throw new Exception('未セットの引数を要求されました。');
    }

    /**
     * @Given /^払戻し情報の配列が返されること（配列の中身はrefundが (\d+)、refundItemが (\d+)）$/
     */
    public function kuaiBiDaelkuaiChaRefundRefunditemDaelg($arg1, $arg2)
    {
        $mock   = new BehatMock();
        $return = $this->_return;

        $mock->assertTrue(count($return) == $arg1);
        $mock->assertTrue(count($return[0]['refundItems']) == $arg2);
    }

}
