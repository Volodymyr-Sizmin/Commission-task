<?php

namespace PayX\CommissionTask\DTO;

class CommissionData
{
    public int $userId;
    public float $amount;
    public string $currency;
    public string $operationDate;
    public array $privateWithdrawCounts;
    public string $operationType;
    public string $userType;

    public function __construct(
        int $userId,
        float $amount,
        string $currency,
        string $operationDate,
        array &$privateWithdrawCounts,
        string $operationType,
        string $userType
    ) {
        $this->userId = $userId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->operationDate = $operationDate;
        $this->privateWithdrawCounts = &$privateWithdrawCounts;
        $this->operationType = $operationType;
        $this->userType = $userType;
    }
}
