<?php

namespace PayX\CommissionTask\Interfaces;

use PayX\CommissionTask\DTO\CommissionData;

interface CommissionCalculatorInterface
{
    public function calculateCommission(CommissionData $data): float;

    public function isApplied($operation, $client);


}
