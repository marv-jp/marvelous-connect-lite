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
class Validate_PaymentPlatformContext extends BehatContext
{

    /**
     * @Given /^: バリデータ：ペイメントプラットフォームID検証を実行する$/
     */
    public function vdaepIdZhouChan()
    {
        $context     = $this->getMainContext();
        $mockContext = $this->getMainContext()->getSubcontext('Mock_alias');

        try {
            $validator = new Validate_PaymentPlatformId();
            $validator->setPaymentPlatformMapper($mockContext->getMockByNum(1));
            $return    = $validator->isValid($context->getArg(1));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

    /**
     * @Given /^: バリデータ：ペイメントプラットフォームID検証\(isNotValid\)を実行する$/
     */
    public function vdaepIdZhouIsnotvalidChan()
    {
        $context     = $this->getMainContext();
        $mockContext = $this->getMainContext()->getSubcontext('Mock_alias');

        try {
            $validator = new Validate_PaymentPlatformId();
            $validator->setPaymentPlatformMapper($mockContext->getMockByNum(1));
            $return    = $validator->isNotValid($context->getArg(1));
            $context->setReturn($return);
        } catch (Exception $exc) {
            $context->setReturn($exc);
        }
    }

}
