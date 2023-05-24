<?php

declare(strict_types=1);

namespace PayX\CommissionTask\Service;

class PrivateWithdrawCountTracker
{
    private array $counts;

    public function __construct()
    {
        $this->counts = [];
    }

    public function incrementCount(int $userId, string $weekKey): int
    {
        if (!isset($this->counts[$userId])) {
            $this->counts[$userId] = [];
        }

        if (!isset($this->counts[$userId][$weekKey])) {
            $this->counts[$userId][$weekKey] = [
                'count' => 0,
                'used_amount' => 0,
            ];
        }

        return $this->counts[$userId][$weekKey]['count']++;
    }

    public function getWeeklyWithdrawCount(int $userId, string $weekKey): int
    {

        return $this->counts[$userId][$weekKey]['count'] ?? 0;
    }

    public function incrementUsedAmount(int $userId, string $weekKey, float $amount): void
    {
        if (!isset($this->counts[$userId])) {
            $this->counts[$userId] = [];
        }

        if (!isset($this->counts[$userId][$weekKey])) {
            $this->counts[$userId][$weekKey] = [
                'count' => 0,
                'used_amount' => 0,
            ];
        }

        $this->counts[$userId][$weekKey]['used_amount'] += $amount;
    }

    public function getUsedAmount(int $userId, string $weekKey): float
    {
        return $this->counts[$userId][$weekKey]['used_amount'] ?? 0;
    }
}
