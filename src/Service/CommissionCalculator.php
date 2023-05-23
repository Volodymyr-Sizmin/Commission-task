<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

use Exception;
use PayX\CommissionTask\DTO\CommissionData;
use PayX\CommissionTask\Interfaces\CommissionCalculatorInterface;

class CommissionCalculator
{
    private array $commissionCalculators;

    private array $data;

    public function __construct(array $data, array $commissionCalculators)
    {
        $this->commissionCalculators = $commissionCalculators;
        $this->data = $data;
    }

    public function calculateCommissionFees(): array
    {
        $commissionFees = [];
        $privateWithdrawCounts = [];

        foreach ($this->data as $row) {
            // Create a CommissionData object from the  data from the row $data
            $commissionData = new CommissionData(
            (int)$row[1],
            (float)$row[4],
            $row[5],
            $row[0],
            $privateWithdrawCounts,
            $row[3],
            $row[2]
            );

            $calculator = $this->getCalculator($commissionData);
            $commissionFee = $calculator->calculateCommission($commissionData);

            $commissionFees[] = $commissionFee;
        }

        return $commissionFees;
    }

    private function getCalculator(CommissionData $data): CommissionCalculatorInterface
    {
        foreach ($this->commissionCalculators as $calculator) {
            if ($calculator->isApplied($data->operationType, $data->userType)) {
                return $calculator;
            }
        }

        throw new Exception('Unsupported operation or client type');
    }
}
