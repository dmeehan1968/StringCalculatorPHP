<?php

namespace Context;

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;
use StringCalculator\StringCalculator;
use StringCalculator\StringCalculatorException;

class StringCalculatorContext implements Context
{
    private StringCalculator $calc;
    /** @var mixed */
    private $result;

    /**
     * @Given /^a calculator$/
     */
    public function aCalculator()
    {
        $this->calc = new StringCalculator();
    }

    /**
     * @When /^the input is "([^"]*)"$/
     */
    public function theInputIs($string)
    {
        try {
            $this->result = $this->calc->add(stripcslashes($string));
        } catch (StringCalculatorException $e) {
            $this->result = $e;
        }
    }

    /**
     * @Then /^the result is (.*)$/
     */
    public function theResultIs($result)
    {
        Assert::assertSame(intval($result), $this->result);
    }

    /**
     * @Then /^there is an exception "([^"]*)"$/
     */
    public function thereIsAnException($message)
    {
        Assert::assertInstanceOf(StringCalculatorException::class, $this->result);
        Assert::assertSame($message, $this->result->getMessage());
    }

}