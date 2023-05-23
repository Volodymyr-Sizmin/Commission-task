<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

use Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

function calculateCommissionFees(string $filename, Currency $currency): array
{
    try {
        $csvParser = new CsvParser($filename);
        $parsedData = $csvParser->parseFile();

        $currencyRates = $currency->getCurrencyRates();
        $commissionCalculators = [
            new DepositCommissionCalculator(new Rounding()),
            new PrivateWithdrawCommissionCalculator($currencyRates, new Rounding(), new PrivateWithdrawCountTracker()),
            new BusinessWithdrawCommissionCalculator(new Rounding()),
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
