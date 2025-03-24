<?php

namespace App\Models;

use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\TransactionStatus;
use App\Domain\ValueObjects\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMoney(): Money
    {
        return new Money($this->amount);
    }

    public function getType(): TransactionType
    {
        return new TransactionType($this->type);
    }

    public function getStatus(): TransactionStatus
    {
        return new TransactionStatus($this->status);
    }

    public function markAsCompleted(): void
    {
        $this->status = TransactionStatus::COMPLETED;
        $this->save();
    }

    public function markAsFailed(): void
    {
        $this->status = TransactionStatus::FAILED;
        $this->save();
    }
} 