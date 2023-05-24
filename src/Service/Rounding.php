<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

class Rounding
{
    public function roundUp(float $amount, string $currency): float
    {
        $decimalPlaces = $this->getCurrencyDecimalPlaces($currency);
        $multiplier = 10 ** $decimalPlaces;

        return ceil($amount * $multiplier) / $multiplier;
    }

    public function getCurrencyDecimalPlaces(string $currency): int
    {
        return ($currency === 'JPY') ? 0 : 2;
    }
}
