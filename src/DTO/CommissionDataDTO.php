<?php

declare(strict_types=1);

namespace PayX\CommissionTask\DTO;

class CommissionDataDTO
{
    public int $userId;
    public float $amount;
    public string $currency;
    public string $operationDate;
    public string $operationType;
    public string $userType;

    public function __construct(
        int    $userId,
        float  $amount,
        string $currency,
        string $operationDate,
        string $operationType,
        string $userType
    )
    {
        $this->userId = $userId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->operationDate = $operationDate;
        $this->operationType = $operationType;
        $this->userType = $userType;
    }
}
