<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service\Calculators;

use PayX\CommissionTask\DTO\CommissionDataDTO;
use PayX\CommissionTask\Interfaces\CommissionCalculatorInterface;
use PayX\CommissionTask\Service\Rounding;

class BusinessWithdrawCommissionCalculator implements CommissionCalculatorInterface
{
    private const BUSINESS_WITHDRAW_COMMISSION_RATE = 0.005; // 0.5%

    private Rounding $rounding;

    public function __construct(Rounding $rounding)
    {
        $this->rounding = $rounding;
    }

    public function calculateCommission(CommissionDataDTO $data): float
    {
        return $this->rounding->roundUp($data->amount * self::BUSINESS_WITHDRAW_COMMISSION_RATE, $data->currency);
    }

    public function isApplied(CommissionDataDTO $data): bool
    {
        return $data->operationType === 'withdraw' && $data->userType === 'business';
    }
}
