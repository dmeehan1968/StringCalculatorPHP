<?php


namespace StringCalculator;

use Exception;

class ThrowExceptionIfAnyNegatives {
    public function __invoke(MutableArrayInterface $array)
    {
        $array
            ->filter(fn($v) => $v < 0)
            ->hasAtLeast(1, function (MutableArrayInterface $array) {
                throw new Exception('Negatives not allowed, got ' . $array->join(', '));
            });
    }
}

class StringCalculator implements StringCalculatorInterface
{
    private string $delimiters = "\n,";
    private iterable $multiDelimiter;

    public function __construct()
    {
        $this->multiDelimiter = new FluentArray();
    }

    /**
     * @throws Exception
     */
    public function add(string $string): int
    {
        $string = stripcslashes($string);
        $numbers = new FluentArray();
        $scanner = new StringScanner($string);

        if ($scanner->scanString('//')) {
            while ($scanner->scanString('[')) {
                if (!$scanner->scanUpToString(']', $this->multiDelimiter->append()->last())
                    || strlen($this->multiDelimiter->last()) < 1) {
                    throw new Exception("Missing closing bracket on multibyte delimiter");
                }
                $scanner->scanString("]");
            }

            if ($this->multiDelimiter->count() == 0) {
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
                if ($this->multiDelimiter->count()) {
                    $found = false;
                    $this->multiDelimiter->each(function ($v) use ($scanner, &$found) {
                        $found = $scanner->scanString($v);
                        return !$found;
                    });
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

        return $numbers
            ->map(fn($v) => trim($v))
            ->map(fn($v) => intval($v))
            ->withMutableCopy(new ThrowExceptionIfAnyNegatives())
            ->filter(fn($v) => $v <= 1000)
            ->sum();

    }
}