<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Tests\Service;

use PayX\CommissionTask\Service\BusinessWithdrawCommissionCalculator;
use PayX\CommissionTask\Service\CommissionCalculator;
use PayX\CommissionTask\Service\DepositCommissionCalculator;
use PayX\CommissionTask\Service\PrivateWithdrawCommissionCalculator;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    private CommissionCalculator $calculator;
    private array $data = [
        ['2014-12-31', 4, 'private', 'withdraw', 1200.00, 'EUR'],
        ['2015-01-01', 4, 'private', 'withdraw', 1000.00, 'EUR'],
        ['2016-01-05', 4, 'private', 'withdraw', 1000.00, 'EUR'],
        ['2016-01-05', 1, 'private', 'deposit', 200.00, 'EUR'],
        ['2016-01-06', 2, 'business', 'withdraw', 300.00, 'EUR'],
        ['2016-01-06', 1, 'private', 'withdraw', 30000.00, 'JPY'],
        ['2016-01-07', 1, 'private', 'withdraw', 1000.00, 'EUR'],
        ['2016-01-07', 1, 'private', 'withdraw', 100.00, 'USD'],
        ['2016-01-10', 1, 'private', 'withdraw', 100.00, 'EUR'],
        ['2016-01-10', 2, 'business', 'deposit', 10000.00, 'EUR'],
        ['2016-01-10', 3, 'private', 'withdraw', 1000.00, 'EUR'],
        ['2016-02-15', 1, 'private', 'withdraw', 300.00, 'EUR'],
        ['2016-02-19', 5, 'private', 'withdraw', 3000000, 'JPY'],
    ];

    protected function setUp(): void
    {
        $currencyRates = [
            'EUR' => 1,
            'USD' => 1.1497,
            'JPY' => 129.53
        ];
        $commissionCalculators = [
            'deposit' => new DepositCommissionCalculator(),
            'withdraw' => [
                'private' => new PrivateWithdrawCommissionCalculator($currencyRates),
                'business' => new BusinessWithdrawCommissionCalculator(),
            ],
        ];
        $this->calculator = new CommissionCalculator($this->data, $commissionCalculators);
    }

    /**
     * @dataProvider CommissionDataProvider
     */

    public function testCalculateCommissionFees(array $data, array $expectedResult): void
    {
        $actualResult = $this->calculator->calculateCommissionFees();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function commissionDataProvider(): array
    {
        return [
            [$this->data, [0.6, 3.00, 0, 0.06, 1.5, 0, 0.7, 0.3, 0.3, 3.0, 0, 0, 8612]],
        ];
    }
}
