<?php


namespace StringCalculator;

use Exception;

class StringCalculator implements StringCalculatorInterface
{

    /**
     * @throws Exception
     */
    private function validate(string $value): bool {
        if (strlen($value) == 0) {
            throw new Exception('Invalid input string');
        }
        return true;
    }

    private function sanitize(array $values): array {
        $values = array_map('trim', $values);
        return array_filter($values, [$this, 'validate']);
    }

    public function add(string $string): int
    {
        $string = stripcslashes($string);
        if (strlen($string) == 0) {
            return 0;
        }
        $numbers = preg_split('/[\n,]/', $string);
        $numbers = $numbers ? $this->sanitize($numbers) : false;
        return ($numbers != false) ? array_reduce($numbers, function ($result, $number) {
            return $result + intval($number);
        }, 0) : 0;

    }
}