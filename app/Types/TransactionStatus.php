<?php

namespace App\Domain\ValueObjects;

class TransactionStatus
{
    public const PENDING = 'pending';
    public const COMPLETED = 'completed';
    public const FAILED = 'failed';

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [self::PENDING, self::COMPLETED, self::FAILED])) {
            throw new \InvalidArgumentException('Invalid transaction status');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->value === self::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->value === self::FAILED;
    }
} 