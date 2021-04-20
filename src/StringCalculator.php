<?php


namespace StringCalculator;

use Cocur\Chain\Chain;
use Exception;

class StringCalculator implements StringCalculatorInterface
{
    private string $delimiters = "\n,";
    private array $multiDelimiter = [];

    /**
     * @throws Exception
     */
    public function add(string $string): int
    {
        $string = stripcslashes($string);
        $numbers = [];
        $scanner = new StringScanner($string);

        if ($scanner->scanString('//')) {
            while ($scanner->scanString('[')) {
                if (!$scanner->scanUpToString(']', $this->multiDelimiter[count($this->multiDelimiter)])
                    || strlen($this->multiDelimiter[count($this->multiDelimiter)-1]) < 1) {
                    throw new Exception("Invalid input string");
                }
                if (!$scanner->scanString("]")) {
                    throw new Exception("Invalid input string");
                }
            }

            if (count($this->multiDelimiter) == 0) {
                if (!$scanner->scanUpToString("\n", $this->delimiters) || strlen($this->delimiters) < 1) {
                    throw new Exception("Invalid input string");
                }
            }

            if (!$scanner->scanString("\n")) {
                throw new Exception("Invalid input string");
            }

        }

        while (!$scanner->atEnd()) {
            $number = null;
            if ($scanner->scanCharactersFromSet('-0123456789', $number)) {
                $numbers[] = $number;
            } else {
                throw new Exception("Invalid input string");
            }
            if (!$scanner->atEnd()) {
                if (count($this->multiDelimiter) > 0) {
                    $found = false;
                    foreach ($this->multiDelimiter as $delimiter) {
                        if ($scanner->scanString($delimiter)) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        throw new Exception("Invalid input string");
                    }
                } else {
                    $delimiter = null;
                    if (!$scanner->scanCharactersFromSet($this->delimiters, $delimiter)) {
                        throw new Exception("Invalid input string");
                    }
                    if (strlen($delimiter) > 1) {
                        throw new Exception("Invalid input string");
                    }
                }
            }
        }

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