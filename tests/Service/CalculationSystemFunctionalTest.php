<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Tests\Service;

use PayX\CommissionTask\Service\CalculationSystem;
use PHPUnit\Framework\TestCase;
use PayX\CommissionTask\Service\Currency;
use Exception;

class CalculationSystemFunctionalTest extends TestCase
{
    private string $filename;
    private Currency $currency;
    private CalculationSystem $calculationSystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filename = '/../../tests/Service/test_data.csv';
        $this->currency = $this->createStub(Currency::class);

        $this->currency = $this->getMockBuilder(Currency::class)
            ->onlyMethods(['getCurrencyRates'])
            ->getMock();

        $this->currency->method('getCurrencyRates')
            ->willReturn([
                'USD' => 1.1497,
                'EUR' => 1,
                'JPY' => 129.53
            ]);

        $this->calculationSystem = new CalculationSystem();
    }

    public function testCalculateCommissionFees(): void
    {
        ob_start();
        try {
            $this->calculationSystem->calculateCommissionFees($this->filename, $this->currency);
            $output = ob_get_clean();
        } catch (Exception $e) {
            $output = ob_get_clean();
            $this->fail('Exception occurred: ' . $e->getMessage());
        }


        $expectedOutput = "0.6\n3\n0\n0.06\n1.5\n0\n0.7\n0.3\n0.3\n3\n0\n0\n8612\n";

        $this->assertEquals($expectedOutput, $output);
    }
}
