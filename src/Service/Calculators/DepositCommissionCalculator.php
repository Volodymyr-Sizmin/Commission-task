<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service\Calculators;

use PayX\CommissionTask\DTO\CommissionDataDTO;
use PayX\CommissionTask\Interfaces\CommissionCalculatorInterface;
use PayX\CommissionTask\Service\Rounding;

class DepositCommissionCalculator implements CommissionCalculatorInterface
{
    private const DEPOSIT_COMMISSION_RATE = 0.0003; // 0.03%

    private Rounding $rounding;

    public function __construct(Rounding $rounding)
    {
        $this->rounding = $rounding;
    }

    public function calculateCommission(CommissionDataDTO $data): float
    {
        return $this->rounding->roundUp($data->amount * self::DEPOSIT_COMMISSION_RATE, $data->currency);
    }

    public function isApplied(CommissionDataDTO $data): bool
    {
        return $data->operationType === 'deposit' && ($data->userType === 'private' || $data->userType === 'business');
    }
}
