<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

use  Carbon\Carbon;
use PayX\CommissionTask\Interfaces\PrivateWithdrawCommissionCalculatorInterface;

class PrivateWithdrawCommissionCalculator implements PrivateWithdrawCommissionCalculatorInterface
{
    private const PRIVATE_WITHDRAW_COMMISSION_RATE = 0.003; // 0.3%
    private const PRIVATE_WITHDRAW_FREE_AMOUNT = 1000.00;
    private const PRIVATE_WITHDRAW_FREE_OPERATIONS = 3;

    private array $currencyRates;

    public function __construct(array $currencyRates)
    {
        $this->currencyRates = $currencyRates;
    }

    public function calculatePrivateWithdrawCommission(
        int    $userId,
        float  $amount,
        string $currency,
        string $operationDate,
        array  &$privateWithdrawCounts
    ): float
    {
        $commissionFee = 0;
        $weekStart = Carbon::parse($operationDate)->startOfWeek();
        $weekEnd = Carbon::parse($operationDate)->endOfWeek();
        $weekKey = $userId . '_' . $weekStart->format('Y-m-d') . '_' . $weekEnd->format('Y-m-d');

        if (!isset($privateWithdrawCounts[$weekKey])) {
            $privateWithdrawCounts[$weekKey] = [
                'count' => 0,
                'used_amount' => 0,
            ];
        }

        $weeklyWithdrawCount = ++$privateWithdrawCounts[$weekKey]['count'];
        $currencyRate = $this->currencyRates[$currency];

        if ($weeklyWithdrawCount <= self::PRIVATE_WITHDRAW_FREE_OPERATIONS) {
            $remainingFreeAmount = (self::PRIVATE_WITHDRAW_FREE_AMOUNT
                - $privateWithdrawCounts[$weekKey]['used_amount']);
            $amountBaseCurrency = $amount / $currencyRate;
            $exceededAmount = max($amount - $remainingFreeAmount * $currencyRate, 0);
            $privateWithdrawCounts[$weekKey]['used_amount'] += min($amountBaseCurrency, $remainingFreeAmount);
            $commissionFee = Currency::roundUp($exceededAmount * self::PRIVATE_WITHDRAW_COMMISSION_RATE, $currency);
        } else {
            $commissionFee = Currency::roundUp($amount * self::PRIVATE_WITHDRAW_COMMISSION_RATE, $currency);
        }

        return $commissionFee;
    }
}


