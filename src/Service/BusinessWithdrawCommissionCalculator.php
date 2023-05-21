<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

use PayX\CommissionTask\Interfaces\CommissionCalculatorInterface;

class BusinessWithdrawCommissionCalculator implements CommissionCalculatorInterface
{
    private const BUSINESS_WITHDRAW_COMMISSION_RATE = 0.005; // 0.5%

    public function calculateCommission(float $amount, string $currency): float
    {
        return Currency::roundUp($amount * self::BUSINESS_WITHDRAW_COMMISSION_RATE, $currency);
    }



}