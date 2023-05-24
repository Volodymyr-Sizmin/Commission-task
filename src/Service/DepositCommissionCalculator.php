<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

use PayX\CommissionTask\DTO\CommissionDataDTO;
use PayX\CommissionTask\Interfaces\CommissionCalculatorInterface;

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

    public function isApplied($operation, $client): bool
    {
        return $operation === 'deposit' && ($client === 'private' || $client === 'business');
    }
}
