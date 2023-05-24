<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

use  Carbon\Carbon;
use PayX\CommissionTask\DTO\CommissionDataDTO;
use PayX\CommissionTask\Interfaces\CommissionCalculatorInterface;

class PrivateWithdrawCommissionCalculator implements CommissionCalculatorInterface
{
    private const PRIVATE_WITHDRAW_COMMISSION_RATE = 0.003; // 0.3%
    private const PRIVATE_WITHDRAW_FREE_AMOUNT = 1000.00;
    private const PRIVATE_WITHDRAW_FREE_OPERATIONS = 3;

    private array $currencyRates;
    private Rounding $rounding;
    private PrivateWithdrawCountTracker $countTracker;

    public function __construct(array $currencyRates, Rounding $rounding, PrivateWithdrawCountTracker $countTracker)
    {
        $this->currencyRates = $currencyRates;
        $this->rounding = $rounding;
        $this->countTracker = $countTracker;
    }

    public function calculateCommission(CommissionDataDTO $data): float
    {
        $commissionFee = 0;
        $weekStart = Carbon::parse($data->operationDate)->startOfWeek();
        $weekEnd = Carbon::parse($data->operationDate)->endOfWeek();
        $weekKey = $data->userId . '_' . $weekStart->format('Y-m-d') . '_' . $weekEnd->format('Y-m-d');

        $weeklyWithdrawCount = $this->countTracker->getWeeklyWithdrawCount($data->userId, $weekKey);
        $weeklyWithdrawCount = $this->countTracker->incrementCount($data->userId, $weekKey);
        $currencyRate = $this->currencyRates[$data->currency];

        if ($weeklyWithdrawCount < self::PRIVATE_WITHDRAW_FREE_OPERATIONS) {
            $remainingFreeAmount = (self::PRIVATE_WITHDRAW_FREE_AMOUNT
                - $this->countTracker->getUsedAmount($data->userId, $weekKey));
            $amountBaseCurrency = $data->amount / $currencyRate;
            $exceededAmount = max($data->amount - $remainingFreeAmount * $currencyRate, 0);
            $this->countTracker->incrementUsedAmount(
                $data->userId, $weekKey,
                min($amountBaseCurrency, $remainingFreeAmount));
            $commissionFee = $this->rounding->roundUp(
                $exceededAmount * self::PRIVATE_WITHDRAW_COMMISSION_RATE,
                $data->currency
            );
        } else {
            $commissionFee = $this->rounding->roundUp(
                $data->amount * self::PRIVATE_WITHDRAW_COMMISSION_RATE,
                $data->currency
            );
        }

        return $commissionFee;
    }

    public function isApplied($operation, $client): bool
    {
        return $operation === 'withdraw' && $client === 'private';
    }
}
