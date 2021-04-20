<?php


namespace StringCalculator;

use Cocur\Chain\Chain;
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
        return true;
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
        $numbers = new Chain($numbers ?? []);
        $numbers
            ->map(fn($v) => trim($v))
            ->filter(function ($v) {
                if (strlen($v) < 1) {
                    throw new Exception('Invalid input string');
                }
                return true;
            })
            ->map(fn($v) => intval($v))
            ->filter(fn($v) => $v <= 1000);

        $negatives = (clone $numbers)->filter(fn($v) => $v < 0);
        if ($negatives->count() > 0) {
            throw new Exception('Negatives not allowed, got ' . $negatives->join(', '));
        }

        return $numbers->sum();

    }
}