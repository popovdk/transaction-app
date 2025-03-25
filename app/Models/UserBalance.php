<?php

namespace App\Models;

use App\Types\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBalance extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMoney(): Money
    {
        return new Money($this->balance);
    }

    public function updateBalance(Money $money): void
    {
        $this->balance = $money->getAmount();
        $this->save();
    }
} 