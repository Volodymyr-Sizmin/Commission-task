<?php
declare(strict_types = 1);

namespace PayX\CommissionTask\Service;


use Carbon\Carbon;
use Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class CommissionCalculator
{
    private const PRIVATE_WITHDRAW_COMMISSION_RATE = 0.003; // 0.3%
    private const PRIVATE_WITHDRAW_FREE_AMOUNT = 1000.00;
    private const PRIVATE_WITHDRAW_FREE_OPERATIONS = 3;
    private const BUSINESS_WITHDRAW_COMMISSION_RATE = 0.005; // 0.5%
    private const DEPOSIT_COMMISSION_RATE = 0.0003; // 0.03%

    private $currencyRates;

    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;

        $this->currencyRates = Currency::getCurrencyRates();
    }

    public function calculateCommissionFees(): array
    {
        $csvParser = new CsvParser($this->filename);
        $data = $csvParser->parseFile();

        $commissionFees = [];
        $privateWithdrawCounts = [];

        foreach ($data as $row) {
            $operationDate = $row[0];
            $userId = (int)$row[1];
            $userType = $row[2];
            $operationType = $row[3];
            $operationAmount = (float)$row[4];
            $operationCurrency = $row[5];

            $commissionFee = 0;

            if ($operationType === 'deposit') {
                $commissionFee = $this->calculateDepositCommission($operationAmount, $operationCurrency);
            } elseif ($operationType === 'withdraw') {
                if ($userType === 'private') {
                    $commissionFee = $this->calculatePrivateWithdrawCommission(
                        $userId,
                        $operationAmount,
                        $operationCurrency,
                        $operationDate,
                        $privateWithdrawCounts
                    );
                } elseif ($userType === 'business') {
                    $commissionFee = $this->calculateBusinessWithdrawCommission($operationAmount, $operationCurrency);
                }
            }

            $commissionFees[] = $commissionFee;
        }

        return $commissionFees;
    }

    private function calculateDepositCommission(float $amount, string $currency): float
    {
        return $this->roundUp($amount * self::DEPOSIT_COMMISSION_RATE, $currency);
    }

    private function calculatePrivateWithdrawCommission(
        int    $userId,
        float  $amount,
        string $currency,
        string $operationDate,
        array  &$privateWithdrawCounts
    ): float
    {
        $commissionFee = 0;
        $weekStart = Carbon::parse($operationDate)->startOfWeek();
        $weekEnd = Carbon::parse($operationDate)->endOfWeek();
        $weekKey = $userId . '_' . $weekStart->format('Y-m-d') . '_' . $weekEnd->format('Y-m-d');

        if (!isset($privateWithdrawCounts[$weekKey])) {
            $privateWithdrawCounts[$weekKey] = [
                'count' => 0,
                'used_amount' => 0,
            ];
        }

        $weeklyWithdrawCount = ++$privateWithdrawCounts[$weekKey]['count'];
        $currencyRate = $this->currencyRates[$currency];

        if ($weeklyWithdrawCount <= self::PRIVATE_WITHDRAW_FREE_OPERATIONS) {
            $remainingFreeAmount = (self::PRIVATE_WITHDRAW_FREE_AMOUNT
                - $privateWithdrawCounts[$weekKey]['used_amount']);
            $amountBaseCurrency = $amount / $currencyRate;
            $exceededAmount = max($amount - $remainingFreeAmount * $currencyRate, 0);
            $privateWithdrawCounts[$weekKey]['used_amount'] += min($amountBaseCurrency, $remainingFreeAmount);
            $commissionFee = $this->roundUp($exceededAmount * self::PRIVATE_WITHDRAW_COMMISSION_RATE, $currency);
        } else {
            $commissionFee = $this->roundUp($amount * self::PRIVATE_WITHDRAW_COMMISSION_RATE, $currency);
        }

        return $commissionFee;
    }

    private function calculateBusinessWithdrawCommission(float $amount, string $currency): float
    {
        return $this->roundUp($amount * self::BUSINESS_WITHDRAW_COMMISSION_RATE, $currency);
    }

    private function roundUp(float $amount, string $currency): float
    {
        $decimalPlaces = $this->getCurrencyDecimalPlaces($currency);
        $multiplier = 10 ** $decimalPlaces;
        return ceil($amount * $multiplier) / $multiplier;
    }

    private function getCurrencyDecimalPlaces(string $currency): int
    {
        return ($currency === 'JPY') ? 0 : 2;
    }
}
$calculator = new CommissionCalculator('input.csv');
$commissionFees = $calculator->calculateCommissionFees();
print_r($commissionFees);