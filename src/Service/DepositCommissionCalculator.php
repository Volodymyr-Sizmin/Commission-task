<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

use PayX\CommissionTask\Interfaces\CommissionCalculatorInterface;

class DepositCommissionCalculator implements CommissionCalculatorInterface
{
    private const DEPOSIT_COMMISSION_RATE = 0.0003; // 0.03%

    public function calculateCommission(float $amount, string $currency): float
    {
        return currency::roundUp($amount * self::DEPOSIT_COMMISSION_RATE, $currency);
    }

}