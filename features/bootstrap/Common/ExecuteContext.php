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
class Common_ExecuteContext extends BehatContext
{

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドを実行し変数 "([^"]*)" に格納、引数はなし$/
     */
    public function vChanmu($arg1, $arg2, $arg3)
    {
        $context = $this->getMainContext();
        try {

            $logic = $context->getValue($arg1);

            $context->setValue($arg3, $logic->$arg2());

            $context->setValue($arg1, $logic);
        } catch (Exception $exc) {
            $context->setValue($arg1, $exc);
        }
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドを実行し変数 "([^"]*)" に格納、引数は変数 "([^"]*)" になる$/
     */
    public function vChanFu($arg1, $arg2, $arg3, $arg4)
    {
        $context = $this->getMainContext();
        try {

            $logic  = $context->getValue($arg1);
            $value1 = $context->getValue($arg4);

            $context->setValue($arg3, $logic->$arg2($value1));

            $context->setValue($arg1, $logic);
        } catch (Exception $exc) {
            $context->setValue($arg1, $exc);
        }
    }

    /**
     * @Given /^変数 "([^"]*)" の "([^"]*)" メソッドを実行し変数 "([^"]*)" に格納、引数は変数 "([^"]*)" "([^"]*)" "([^"]*)" "([^"]*)" になる$/
     */
    public function vChanFu2($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
    {
        $context = $this->getMainContext();
        try {

            $logic  = $context->getValue($arg1);
            $value1 = $context->getValue($arg4);
            $value2 = $context->getValue($arg5);
            $value3 = $context->getValue($arg6);
            $value4 = $context->getValue($arg7);

            $context->setValue($arg3, $logic->$arg2($value1, $value2, $value3, $value4));

            $context->setValue($arg1, $logic);
        } catch (Exception $exc) {
            $context->setValue($arg1, $exc);
        }
    }

}
