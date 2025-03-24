<?php

namespace App\Jobs;

use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $transactionId
    ) {}

    public function handle(TransactionService $transactionService): void
    {
        $transaction = Transaction::findOrFail($this->transactionId);
        $user = User::findOrFail($transaction->user_id);

        $transactionService->processTransaction(
            $user,
            $transaction->getMoney(),
            $transaction->getType(),
            $transaction->description
        );
    }

    public function failed(\Throwable $exception): void
    {
        $transaction = Transaction::find($this->transactionId);
        if ($transaction) {
            $transaction->markAsFailed();
        }
    }
} 