<?php

namespace Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use PHPUnit\Framework\Assert;
use StringCalculator\StringCalculator;

class StringCalculatorContext implements Context
{
    private StringCalculator $calc;
    private int $result;

    /**
     * @Given /^there is a string calculator$/
     */
    public function thereIsAStringCalculator()
    {
        $this->calc = new StringCalculator();
    }

    /**
     * @Then /^it implements the "([^"]*)" interface$/
     */
    public function itImplementsTheInterface($interface)
    {
        Assert::assertInstanceOf($interface, $this->calc);
    }

    /**
     * @When /^I add "([^"]*)"$/
     */
    public function iAdd($string)
    {
        $this->result = $this->calc->add($string);
    }

    /**
     * @Then /^the result is (.*)$/
     */
    public function theResultIs($result)
    {
        Assert::assertSame($this->result, intval($result));
    }

}