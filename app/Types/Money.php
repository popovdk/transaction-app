<?php

namespace App\Types;

class Money
{
    private float $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'RUB')
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }

        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Currencies must match');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Currencies must match');
        }

        $newAmount = $this->amount - $other->amount;
        if ($newAmount < 0) {
            throw new \InvalidArgumentException('Resulting amount cannot be negative');
        }

        return new self($newAmount, $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
} 