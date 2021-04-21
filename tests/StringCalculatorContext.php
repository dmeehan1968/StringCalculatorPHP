<?php

namespace Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use PHPUnit\Framework\Assert;
use StringCalculator\StringCalculator;
use Exception;

class StringCalculatorContext implements Context
{
    private StringCalculator $calc;
    /** @var mixed */
    private $result;

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
        try {
            $this->result = $this->calc->add(stripcslashes($string));
        } catch (Exception $e) {
            $this->result = $e;
        }
    }

    /**
     * @Then /^the result is (.*)$/
     */
    public function theResultIs($result)
    {
        Assert::assertSame($this->result, intval($result));
    }

    /**
     * @Then /^there is an exception "([^"]*)"$/
     */
    public function thereIsAnException($message)
    {
        Assert::assertInstanceOf('Exception', $this->result);
        Assert::assertSame($this->result->getMessage(), $message);
    }

}