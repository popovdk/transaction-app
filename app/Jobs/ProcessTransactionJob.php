<?php

namespace App\Jobs;

use App\Domain\ValueObjects\TransactionType;
use App\Models\User;
use App\Services\TransactionService;
use App\Types\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $afterCommit = true;

    public function __construct(
        private User $user,
        private Money $amount,
        private TransactionType $type,
        private string $description
    ) {}

    public function handle(TransactionService $transactionService)
    {
        $transactionService->processTransaction(
            $this->user,
            $this->amount,
            $this->type,
            $this->description
        );
    }
} 