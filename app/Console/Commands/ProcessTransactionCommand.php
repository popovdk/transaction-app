<?php

namespace App\Console\Commands;

use App\Domain\ValueObjects\TransactionType;
use App\Jobs\ProcessTransactionJob;
use App\Models\User;
use Illuminate\Console\Command;
use App\Types\Money;

class ProcessTransactionCommand extends Command
{
    protected $signature = 'transaction:process {email} {amount} {type} {description}';
    protected $description = 'Process a transaction for a user';

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

            ProcessTransactionJob::dispatch($user, $money, $transactionType, $description);

            $this->info('Transaction has been queued for processing');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to queue transaction: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
} 