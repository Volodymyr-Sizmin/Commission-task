<?php

namespace PayX\CommissionTask\Interfaces;

interface PrivateWithdrawCommissionCalculatorInterface
{
    public function calculatePrivateWithdrawCommission(
        int    $userId,
        float  $amount,
        string $currency,
        string $operationDate,
        array  &$privateWithdrawCounts
    ): float;
}
