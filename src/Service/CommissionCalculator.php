<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

use Exception;
use PayX\CommissionTask\DTO\CommissionDataDTO;
use PayX\CommissionTask\Interfaces\CommissionCalculatorInterface;

class CommissionCalculator
{
    private array $commissionCalculators;

    public function __construct(array $commissionCalculators)
    {
        $this->commissionCalculators = $commissionCalculators;
    }

    public function calculateCommissionFee(CommissionDataDTO $data): float
    {
        $calculator = $this->getCalculator($data);

        return $calculator->calculateCommission($data);

    }

    private function getCalculator(CommissionDataDTO $data): CommissionCalculatorInterface
    {
        foreach ($this->commissionCalculators as $calculator) {
            if ($calculator->isApplied($data)) {
                return $calculator;
            }
        }

        throw new Exception('Unsupported operation or client type');
    }
}
