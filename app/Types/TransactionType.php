<?php

namespace App\Domain\ValueObjects;

class TransactionType
{
    public const DEPOSIT = 'deposit';
    public const WITHDRAWAL = 'withdrawal';

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [self::DEPOSIT, self::WITHDRAWAL])) {
            throw new \InvalidArgumentException('Invalid transaction type');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isDeposit(): bool
    {
        return $this->value === self::DEPOSIT;
    }

    public function isWithdrawal(): bool
    {
        return $this->value === self::WITHDRAWAL;
    }
} 