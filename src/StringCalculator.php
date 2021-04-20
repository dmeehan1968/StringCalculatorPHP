<?php


namespace StringCalculator;

interface StringCalculatorInterface {
    public function add(string $string): int;
}

class StringCalculator implements StringCalculatorInterface
{

    public function add(string $string): int
    {
        $numbers = explode(',', $string);
        return $numbers !== false ? array_reduce($numbers, function ($result, $number) {
            return $result + intval($number);
        }, 0) : 0;

    }
}