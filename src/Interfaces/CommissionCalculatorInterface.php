<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Interfaces;

use PayX\CommissionTask\DTO\CommissionDataDTO;

interface CommissionCalculatorInterface
{
    public function calculateCommission(CommissionDataDTO $data): float;

    public function isApplied($operation, $client);


}
