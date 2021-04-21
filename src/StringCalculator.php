<?php

namespace StringCalculator;

class StringCalculator implements StringCalculatorInterface
{
    private string $delimiters = "\n,";
    private iterable $multiDelimiter;

    public function __construct()
    {
        $this->multiDelimiter = new FluentArray();
    }

    /**
     * @throws StringCalculatorException
     */
    public function add(string $string): int
    {
        $scanner = new StringScanner($string);

        $this->getConfig($scanner);

        $numbers = $this->getNumbers($scanner);

        return (new FluentArray($numbers))
            ->map(fn($v) => trim($v))
            ->map(fn($v) => intval($v))
            ->withMutableCopy(new ThrowExceptionIfAnyNegatives())
            ->filter(fn($v) => $v <= 1000)
            ->sum();

    }

    /**
     * @param StringScanner $scanner
     * @throws StringCalculatorException
     */
    private function getConfig(StringScanner $scanner): void
    {
        if ($scanner->scanString('//')) {
            while ($scanner->scanString('[')) {
                if (!$scanner->scanUpToString(']', $this->multiDelimiter->append()->last())
                    || strlen($this->multiDelimiter->last()) < 1) {
                    throw new StringCalculatorException("Missing closing bracket on multibyte delimiter");
                }
                $scanner->scanString("]");
            }

            if ($this->multiDelimiter->count() == 0) {
                if (!$scanner->scanUpToString("\n", $this->delimiters) || strlen($this->delimiters) < 1) {
                    throw new StringCalculatorException("Missing newline after delimiters");
                }
            }

            if (!$scanner->scanString("\n")) {
                throw new StringCalculatorException("Missing newline after delimiters");
            }
        }
    }

    /**
     * @param StringScanner $scanner
     * @return array
     * @throws StringCalculatorException
     */
    private function getNumbers(StringScanner $scanner): array
    {
        $numbers = [];

        while (!$scanner->atEnd()) {
            $number = null;
            if ($scanner->scanCharactersFromSet('-0123456789', $number)) {
                $numbers[] = $number;
            } else {
                throw new StringCalculatorException("Invalid characters where number expected");
            }
            if (!$scanner->atEnd()) {
                if ($this->multiDelimiter->count()) {
                    $found = false;
                    $this->multiDelimiter->each(function ($v) use ($scanner, &$found) {
                        $found = $scanner->scanString($v);
                        return !$found;
                    });
                    if (!$found) {
                        throw new StringCalculatorException("Unexpected delimiter found");
                    }
                } else {
                    $delimiter = null;
                    if (!$scanner->scanCharactersFromSet($this->delimiters, $delimiter)) {
                        throw new StringCalculatorException("Unexpected delimiter found");
                    }
                    if (strlen($delimiter) > 1) {
                        throw new StringCalculatorException("Multiple delimiters encountered");
                    }
                }
            }
        }
        return $numbers;
    }
}