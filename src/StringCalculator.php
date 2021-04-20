<?php


namespace StringCalculator;

use Exception;

class StringCalculator implements StringCalculatorInterface
{
    private string $delimiters = "\n,";

    /**
     * @throws Exception
     */
    private function validateValue(string $value): bool {
        if (strlen($value) == 0) {
            throw new Exception('Invalid input string');
        }
        if (intval($value) < 0) {
            throw new Exception('Negatives not allowed');
        }
        return true;
    }

    private function sanitizeValues(array $values): array {
        $values = array_map('trim', $values);
        return array_filter($values, [$this, 'validateValue']);
    }

    public function add(string $string): int
    {
        $string = stripcslashes($string);
        preg_match('/^(?<config>\/\/(?<delimiter>[^\n]+)\n)?(?<string>.*)$/s', $string, $matches);

        if (strlen($matches['config']) > 0) {
            $this->delimiters = $matches['delimiter'];
        }
        $string = $matches['string'] ?? $string;

        if (strlen($string) == 0) {
            return 0;
        }
        $numbers = preg_split("/[{$this->delimiters}]/", $string);
        $numbers = $numbers ? $this->sanitizeValues($numbers) : false;
        return ($numbers != false) ? array_reduce($numbers, function ($result, $number) {
            return $result + intval($number);
        }, 0) : 0;

    }
}