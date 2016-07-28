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
class Common_ConfirmationContext extends BehatContext
{

    /**
     * @Given /^変数 "([^"]*)" には "([^"]*)" の "([^"]*)" が返却されること$/
     */
    public function bids5($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();

        $result = $context->getValue($arg1);

        $assertValue = '';
        switch ($arg2) {
            case "文字列":
                $assertValue = $arg3;
                break;
            case "真偽値":
                if ('TRUE' == strtoupper($arg3)) {
                    $assertValue = TRUE;
                } else {
                    $assertValue = FALSE;
                }
        }

        $mock = new BehatMock();
        $mock->assertEquals($result, $assertValue);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドで "([^"]*)" クラスが返却される、変数 "([^"]*)" に格納$/
     */
    public function vBiFu($arg1, $arg2, $arg3, $arg4)
    {
        $context = $this->getMainContext();

        $result = $context->getValue($arg1);

        $class = $result->$arg2();

        $className = get_class($class);

        $mock = new BehatMock();
        $mock->assertEquals($className, $arg3);

        $context->setValue($arg4, $class);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドで "([^"]*)" が返却される$/
     */
    public function vBi2($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();

        $result = $context->getValue($arg1);

        $mock = new BehatMock();

        $mock->assertEquals($result->$arg2(), $arg3);
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドを実行$/
     */
    public function vChan($arg1, $arg2)
    {
        $context = $this->getMainContext();

        $logic = $context->getValue($arg1);

        $logic->$arg2();
    }

    /**
     * @Given /^変数 "([^"]*)" には以下の例外が返却されること$/
     */
    public function biExceptionException5($arg1, PyStringNode $string)
    {
        $context = $this->getMainContext();

        $className = get_class($context->getValue($arg1));
        if ($className !== $string->getRaw()) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^変数 "([^"]*)" に例外が発生していないこと$/
     */
    public function liChang($arg1)
    {
        $context = $this->getMainContext();

        $className = get_class($context->getValue($arg1));
        if ($context->getValue($arg1) instanceof Exception) {
            throw new Exception($className);
        }
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドで "([^"]*)" の "([^"]*)" が返却される$/
     */
    public function vBi3($arg1, $arg2, $arg3, $arg4)
    {
        $context = $this->getMainContext();

        $result = $context->getValue($arg1);

        $assertValue = '';
        switch ($arg3) {
            case "文字列":
                $assertValue = $arg4;
                break;
            case "真偽値":
                if ('TRUE' == strtoupper($arg4)) {
                    $assertValue = TRUE;
                } else {
                    $assertValue = FALSE;
                }
        }

        $mock = new BehatMock();
        $mock->assertEquals($result->$arg2(), $assertValue);
    }

    /**
     * @Given /^変数 "([^"]*)" のキー "([^"]*)" で "([^"]*)" が返却される$/
     */
    public function bi3($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();

        $val  = $context->getValue($arg1);
        $mock = new BehatMock();
        $mock->assertEquals($val[$arg2], $arg3);
    }

    /**
     * @Given /^変数 "([^"]*)" のキー "([^"]*)" で (\d+) が返却される$/
     */
    public function bi4($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();

        $val  = $context->getValue($arg1);
        $mock = new BehatMock();
        $mock->assertEquals($val[$arg2], $arg3);
    }

    /**
     * @Given /^変数 "([^"]*)" は "([^"]*)" が返却される$/
     */
    public function bi5($arg1, $arg2)
    {
        $context = $this->getMainContext();

        $val  = $context->getValue($arg1);
        $mock = new BehatMock();
        $mock->assertEquals($val, $arg2);
    }

    /**
     * @Given /^変数 "([^"]*)" は (\d+) が返却される$/
     */
    public function bi35($arg1, $arg2)
    {
        $context = $this->getMainContext();

        $val  = $context->getValue($arg1);
        $mock = new BehatMock();
        $mock->assertEquals($val, $arg2);
    }

}
