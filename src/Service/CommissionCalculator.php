<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

use PayX\CommissionTask\Interfaces\CommissionCalculatorInterface;
use PayX\CommissionTask\Interfaces\PrivateWithdrawCommissionCalculatorInterface;

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
            // Extracting data from the row
            $operationDate = $row[0];
            $userId = (int)$row[1];
            $userType = $row[2];
            $operationType = $row[3];
            $operationAmount = (float)$row[4];
            $operationCurrency = $row[5];

            $commissionFee = 0;
            $calculator = 0;

            // Selecting the appropriate calculator based on operation type and user type
            if (isset($this->commissionCalculators[$operationType])) {
                $calculator = $this->commissionCalculators[$operationType];
                if (is_array($calculator) && isset($calculator[$userType])) {
                    $calculator = $calculator[$userType];
                }
            }

            if ($calculator !== null) {
                // Calculating commission fee based on the selected calculator
                if ($calculator instanceof CommissionCalculatorInterface) {
                    $commissionFee = $calculator->calculateCommission($operationAmount, $operationCurrency);
                } elseif ($calculator instanceof PrivateWithdrawCommissionCalculatorInterface) {
                    $commissionFee = $calculator->calculatePrivateWithdrawCommission(
                        $userId,
                        $operationAmount,
                        $operationCurrency,
                        $operationDate,
                        $privateWithdrawCounts
                    );

                }
            }

            $commissionFees[] = $commissionFee;
        }

        return $commissionFees;
    }
}
