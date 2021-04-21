<?php

namespace StringCalculator;

class ThrowExceptionIfAnyNegatives
{
    public function __invoke(MutableArrayInterface $array)
    {
        $array
            ->filter(fn($v) => $v < 0)
            ->hasAtLeast(1, function (MutableArrayInterface $array) {
                throw new StringCalculatorException('Negatives not allowed, got ' . $array->join(', '));
            });
    }
}