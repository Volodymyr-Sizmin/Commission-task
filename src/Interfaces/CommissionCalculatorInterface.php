<?php

namespace PayX\CommissionTask\Interfaces;

interface CommissionCalculatorInterface
{
    public function calculateCommission(float $amount, string $currency): float;
}
