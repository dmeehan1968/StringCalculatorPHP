<?php


namespace StringCalculator;

interface StringCalculatorInterface {
    public function add(string $numbers): int;
}

class StringCalculator implements StringCalculatorInterface
{

    public function add(string $numbers): int
    {
        $values = explode(',', $numbers);
        if ($values !== false) {
            $result = 0;
            foreach ($values as $value) {
                $result += intval($value);
            }
            return $result;
        }
        return 0;
    }
}