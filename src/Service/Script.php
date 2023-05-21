<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;


use Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

function calculateCommissionFees(string $filename): array
{
    try {
        $csvParser = new CsvParser($filename);
        $parsedData = $csvParser->parseFile();
        print_r($parsedData);

        $currencyRates = Currency::getCurrencyRates();
        $commissionCalculators = [
            'deposit' => new DepositCommissionCalculator(),
            'withdraw' => [
                'private' => new PrivateWithdrawCommissionCalculator($currencyRates),
                'business' => new BusinessWithdrawCommissionCalculator(),
            ]
        ];
        $calculator = new CommissionCalculator($parsedData, $commissionCalculators);

        return $calculator->calculateCommissionFees();
    } catch (Exception $e) {
        echo 'Exception occurred: ' . $e->getMessage();
        return [];
    }

}

$filename = 'input.csv';
$commissionFees = calculateCommissionFees($filename);

print_r($commissionFees);

