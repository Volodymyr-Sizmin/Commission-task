<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

use Exception;
use PayX\CommissionTask\DTO\CommissionDataDTO;

require_once __DIR__ . '/../../vendor/autoload.php';

class CalculationSystem
{
    public function calculateCommissionFees(string $filename, Currency $currency): void
    {
        try {
            $csvParser = new CsvParser($filename);
            $parsedData = $csvParser->parseFile();
            $rounding = new Rounding();
            $currencyRates = $currency->getCurrencyRates();
            $commissionCalculators = [
                new DepositCommissionCalculator($rounding),
                new PrivateWithdrawCommissionCalculator($currencyRates, $rounding, new PrivateWithdrawCountTracker()),
                new BusinessWithdrawCommissionCalculator($rounding),
            ];
            $calculator = new CommissionCalculator($commissionCalculators);

            foreach ($parsedData as $row) {
                $commissionData = new CommissionDataDTO(
                    (int) $row[1],
                    (float) $row[4],
                    $row[5],
                    $row[0],
                    $row[3],
                    $row[2]
                );

                $commissionFee = $calculator->calculateCommissionFee($commissionData);
                echo $commissionFee . PHP_EOL;

            }

        } catch (Exception $e) {
            echo 'Exception occurred: ' . $e->getMessage();
        }
    }
}

$filename = 'input.csv';
$system = new CalculationSystem();
$system->calculateCommissionFees($filename, new Currency());
