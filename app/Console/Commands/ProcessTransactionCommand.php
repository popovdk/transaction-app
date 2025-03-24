<?php

namespace App\Console\Commands;

use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\TransactionType;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Console\Command;

class ProcessTransactionCommand extends Command
{
    protected $signature = 'transaction:process {email} {amount} {type} {description}';
    protected $description = 'Process a transaction for a user';

    public function __construct(
        private readonly TransactionService $transactionService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $email = $this->argument('email');
        $amount = (float) $this->argument('amount');
        $type = $this->argument('type');
        $description = $this->argument('description');

        try {
            $user = User::where('email', $email)->firstOrFail();
            $money = new Money($amount);
            $transactionType = new TransactionType($type);

            $transaction = $this->transactionService->processTransaction(
                $user,
                $money,
                $transactionType,
                $description
            );

            $this->info('Transaction processed successfully');
            $this->info("Transaction ID: {$transaction->id}");
            $this->info("Status: {$transaction->status}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to process transaction: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
} 