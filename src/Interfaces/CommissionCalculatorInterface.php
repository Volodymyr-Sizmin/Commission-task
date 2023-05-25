<?php

namespace PayX\CommissionTask\Interfaces;

use PayX\CommissionTask\DTO\CommissionDataDTO;

interface CommissionCalculatorInterface
{
    public function calculateCommission(CommissionDataDTO $data): float;

    public function isApplied(CommissionDataDTO $data);
}
