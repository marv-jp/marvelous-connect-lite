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
class Common_PreparationContext extends BehatContext
{

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" コレクションがセットされる$/
     */
    public function stepDefinition11($arg1, $arg2)
    {
        $context = $this->getMainContext();
        $context->setValue($arg1, new $arg2());
    }

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" モデルがセットされる$/
     */
    public function stepDefinition12($arg1, $arg2)
    {
        $context = $this->getMainContext();
        $context->setValue($arg1, new $arg2());
    }
    
        /**
     * @Given /^変数 "([^"]*)" には空の配列がセットされる$/
     */
    public function sinKuai($arg1)
    {
        $context = $this->getMainContext();
        $a = array();
        $context->setValue($arg1, $a);
    }


    /**
     * @Given /^変数 "([^"]*)" のプロパティ "([^"]*)" には "([^"]*)" がセットされる$/
     */
    public function stepDefinition13($arg1, $arg2, $arg3)
    {
        $context   = $this->getMainContext();
        $obj       = $context->getValue($arg1);
        $setMethod = Common_Util_String::snakeToCamel('set_' . $arg2);
        $obj->$setMethod($arg3);
        $context->setValue($arg1, $obj);
    }

    /**
     * @Given /^変数 "([^"]*)" のプロパティ "([^"]*)" には (\d+) がセットされる$/
     */
    public function stepDefinition5($arg1, $arg2, $arg3)
    {
        $context   = $this->getMainContext();
        $obj       = $context->getValue($arg1);
        $setMethod = Common_Util_String::snakeToCamel('set_' . $arg2);
        $obj->$setMethod($arg3);
        $context->setValue($arg1, $obj);
    }

    /**
     * @Given /^変数 "([^"]*)" には変数 "([^"]*)" がアタッチされる$/
     */
    public function v6($arg1, $arg2)
    {
        $context    = $this->getMainContext();
        $collection = $context->getValue($arg1);
        $model      = $context->getValue($arg2);
        $collection->attach($model);
        $context->setValue($arg1, $collection);
    }

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" Fixtureの "([^"]*)" を使用する$/
     */
    public function fixtureZhi($arg1, $arg2, $arg3)
    {
        $context  = $this->getMainContext();
        $fixtures = new $arg2();
        $context->setValue($arg1, $fixtures->$arg3);
    }

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" のモックがセットされ、対象メソッドは "([^"]*)" となる$/
     */
    public function naoV($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();

        $mock = new BehatMock();

        $stub = $mock->getMock($arg2, explode(',', $arg3));
        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" のモックがセットされ、引数は "([^"]*)" Fixture "([^"]*)" 対象メソッドは "([^"]*)" となる$/
     */
    public function fixtureNaoV($arg1, $arg2, $arg3, $arg4, $arg5)
    {
        $context = $this->getMainContext();

        $mock    = new BehatMock();
        $fixture = new $arg3();


        $stub = $mock->getMock($arg2, explode(',', $arg5), array($fixture->$arg4));
        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" のモックがセットされ、引数は "([^"]*)" Fixture "([^"]*)" と "([^"]*)" Fixture "([^"]*)" 対象メソッドは "([^"]*)" となる$/
     */
    public function fixtureFixtureNaoV($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
    {
        $context = $this->getMainContext();

        $mock     = new BehatMock();
        $fixture1 = new $arg3();
        $fixture2 = new $arg5();

        $stub = $mock->getMock($arg2, explode(',', $arg7), array($fixture1->$arg4, $fixture2->$arg6));
        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドでは "([^"]*)" Fixtureの "([^"]*)" が返ってくるようモックを設定$/
     */
    public function vFixtureBiZhao($arg1, $arg2, $arg3, $arg4)
    {
        $context = $this->getMainContext();

        $fixture = new $arg3();

        $mock = new BehatMock();
        $stub = $context->getValue($arg1);
        $stub->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($fixture->$arg4));

        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドでは "([^"]*)" の "([^"]*)" が返ってくるようモックを設定$/
     */
    public function vBiZhao($arg1, $arg2, $arg3, $arg4)
    {
        $context = $this->getMainContext();

        $returnValue = '';
        switch ($arg3) {
            case "文字列":
                $returnValue = $arg4;
                break;
            case "真偽値":
                if ('TRUE' == strtoupper($arg4)) {
                    $returnValue = TRUE;
                } else {
                    $returnValue = FALSE;
                }
                break;
        }

        $mock = new BehatMock();
        $stub = $context->getValue($arg1);
        $stub->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($returnValue));

        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドでは "([^"]*)" の (\d+) が返ってくるようモックを設定$/
     */
    public function vBiZhaoNum($arg1, $arg2, $arg3, $arg4)
    {
        $context = $this->getMainContext();

        $returnValue = '';
        switch ($arg3) {
            case "数値":
                $returnValue = $arg4;
                break;
        }

        $mock = new BehatMock();
        $stub = $context->getValue($arg1);
        $stub->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($returnValue));

        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドでは "([^"]*)" の例外が返ってくるようモックを設定$/
     */
    public function vBiZhaoExceptoioio($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();

        $returnValue = '';

        $mock = new BehatMock();
        $stub = $context->getValue($arg1);
        $stub->expects($mock->any())
                ->method($arg2)
                ->will($mock->throwException(new $arg3()));

        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドでは変数 "([^"]*)" が返ってくるようモックを設定$/
     */
    public function vBiZhao2($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();

        $mock        = new BehatMock();
        $stub        = $context->getValue($arg1);
        $returnValue = $context->getValue($arg3);

        $stub->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnValue($returnValue));

        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドを引数 "([^"]*)" で実行する$/
     */
    public function vChan($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();
        $logic   = $context->getValue($arg1);

        $logic->$arg2($arg3);
        $context->setValue($arg1, $logic);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドを引数 変数 "([^"]*)" で実行する$/
     */
    public function vChanHensu($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();
        $logic   = $context->getValue($arg1);

        $property = $context->getValue($arg3);

        $logic->$arg2($property);
        $context->setValue($arg1, $logic);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドを引数 "([^"]*)" Fixture "([^"]*)" で実行する$/
     */
    public function vFixtureChan($arg1, $arg2, $arg3, $arg4)
    {
        $context = $this->getMainContext();
        $logic   = $context->getValue($arg1);

        $fixture = new $arg3();

        $logic->$arg2($fixture->$arg4);
        $context->setValue($arg1, $logic);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドを引数 キー "([^"]*)" 要素 "([^"]*)" の配列で実行する$/
     */
    public function vYiGouKuaiChan($arg1, $arg2, $arg3, $arg4)
    {
        $context = $this->getMainContext();
        $logic   = $context->getValue($arg1);

        $property = array($arg3 => $context->getValue($arg4));

        $logic->$arg2($property);
        $context->setValue($arg1, $logic);
    }

    /**
     * @Given /^変数 "([^"]*)" にはNULLがセットされる$/
     */
    public function nullxxxx($arg1)
    {
        $context = $this->getMainContext();

        $context->setValue($arg1, NULL);
    }

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" の "([^"]*)" がセットされる$/
     */
    public function stepDefinition4($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();

        $returnValue = '';
        switch ($arg2) {
            case "文字列":
                $returnValue = $arg3;
                break;
            case "真偽値":
                if ('TRUE' == strtoupper($arg3)) {
                    $returnValue = TRUE;
                } else {
                    $returnValue = FALSE;
                }
                break;
        }


        $context->setValue($arg1, $returnValue);
    }

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" のモックがセットされ、引数はなし 対象メソッドは "([^"]*)" となる$/
     */
    public function naoVa($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();

        $mock = new BehatMock();
        $stub = $mock->getMock($arg2, explode(',', $arg3));
        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" にはHTTPMockがセットされる$/
     */
    public function httpmock($arg1)
    {
        $context = $this->getMainContext();

        $context->setValue($arg1, $context->getHttpMock());
    }

    /**
     * @Given /^変数 "([^"]*)" には変数 "([^"]*)" の "([^"]*)" メソッドの実行結果がセットされる$/
     */
    public function vChanHuan($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();
        $val     = $context->getValue($arg2);
        $context->setValue($arg1, $val->$arg3());
    }

    /**
     * @Given /^変数 "([^"]*)" のプロパティ "([^"]*)" には "([^"]*)" Fixtureの "([^"]*)" がセットされる$/
     */
    public function fixture($arg1, $arg2, $arg3, $arg4)
    {
        $context   = $this->getMainContext();
        $val       = $context->getValue($arg1);
        $fixtures  = new $arg3();
        $setMethod = Common_Util_String::snakeToCamel('set_' . $arg2);
        $val->$setMethod($fixtures->$arg4);
        $context->setValue($arg1, $val);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドでは (\d+) 番目の引数の値が返ってくるようモックを設定$/
     */
    public function vYuJuBiZhao($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();

        $mock = new BehatMock();
        $stub = $context->getValue($arg1);
        $stub->expects($mock->any())
                ->method($arg2)
                ->will($mock->returnArgument($arg3 - 1));

        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" のプロパティ "([^"]*)" には "([^"]*)" の配列がセットされる$/
     */
    public function kuai($arg1, $arg2, $arg3)
    {
        $context   = $this->getMainContext();
        $val       = $context->getValue($arg1);
        $setMethod = Common_Util_String::snakeToCamel('set_' . $arg2);
        $val->$setMethod(explode(',', $arg3));
        $context->setValue($arg1, $val);
    }

    /**
     * @Given /^変数 "([^"]*)" に "([^"]*)" のapplication\.iniの設定をセットする$/
     */
    public function applicationIniZhao($arg1, $arg2)
    {
        $context = $this->getMainContext();
        $stub    = $context->getValue($arg1);

        $stub->setConfigCategory($arg2);

        // 設定値をAPIインスタンスにセット
        $methods        = get_class_methods($stub);
        $platformConfig = Common_External_Platform::getPlatformInfo($arg2);
        foreach ($platformConfig as $paramKey => $value) {
            // 正規表現でスネークケース方式から、キャメルケース方式に名前を変換            
            $method = 'set' . ucfirst(preg_replace_callback('/_(.)/', function($m) {
                                return strtoupper($m[1]);
                            }, $paramKey));
            if (in_array($method, $methods)) {
                $stub->$method($value);
            }
        }
        $context->setValue($arg1, $stub);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドを実行する$/
     */
    public function vChanhoho($arg1, $arg2)
    {
        $context = $this->getMainContext();
        $logic   = $context->getValue($arg1);

        $logic->$arg2();
        $context->setValue($arg1, $logic);
    }

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" クラスがセットされる$/
     */
    public function stepDefinitikoon4($arg1, $arg2)
    {
        $context = $this->getMainContext();
        $context->setValue($arg1, new $arg2());
    }

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" のAPIインスタンスがセットされる$/
     */
    public function apiV($arg1, $arg2)
    {
        $context = $this->getMainContext();
        $context->setValue($arg1, Common_External_Platform::getInstance($arg2));
    }

    /**
     * @Given /^変数 "([^"]*)" はZendのリクエストインスタンスにセットされる$/
     */
    public function zendV($arg1)
    {
        $context = $this->getMainContext();
        $request = $context->getValue($arg1);

        Zend_Controller_Front::getInstance()->setRequest($request);
    }

}
