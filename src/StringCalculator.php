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
                    throw new Exception("Missing closing bracket on multibyte delimiter");
                }
                $scanner->scanString("]");
            }

            if (count($this->multiDelimiter) == 0) {
                if (!$scanner->scanUpToString("\n", $this->delimiters) || strlen($this->delimiters) < 1) {
                    throw new Exception("Missing newline after delimiters");
                }
            }

            if (!$scanner->scanString("\n")) {
                throw new Exception("Missing newline after delimiters");
            }

        }

        while (!$scanner->atEnd()) {
            $number = null;
            if ($scanner->scanCharactersFromSet('-0123456789', $number)) {
                $numbers[] = $number;
            } else {
                throw new Exception("Invalid characters where number expected");
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
                        throw new Exception("Unexpected delimiter found");
                    }
                } else {
                    $delimiter = null;
                    if (!$scanner->scanCharactersFromSet($this->delimiters, $delimiter)) {
                        throw new Exception("Unexpected delimiter found");
                    }
                    if (strlen($delimiter) > 1) {
                        throw new Exception("Multiple delimiters encountered");
                    }
                }
            }
        }

        $numbers = new Chain($numbers ?? []);
        $numbers
            ->map(fn($v) => trim($v))
            ->map(fn($v) => intval($v))
            ->filter(fn($v) => $v <= 1000);

        $negatives = (clone $numbers)->filter(fn($v) => $v < 0);
        if ($negatives->count() > 0) {
            throw new Exception('Negatives not allowed, got ' . $negatives->join(', '));
        }

        return $numbers->sum();

    }
}