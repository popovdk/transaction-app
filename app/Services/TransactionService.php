<?php

namespace App\Services;

use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\TransactionStatus;
use App\Domain\ValueObjects\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function processTransaction(User $user, Money $amount, TransactionType $type, string $description): Transaction
    {
        return DB::transaction(function () use ($user, $amount, $type, $description) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount->getAmount(),
                'type' => $type->getValue(),
                'description' => $description,
                'status' => TransactionStatus::PENDING,
            ]);

            try {
                $this->updateUserBalance($user, $amount, $type);
                $transaction->markAsCompleted();
            } catch (\Exception $e) {
                Log::error('Transaction processing failed', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
                $transaction->markAsFailed();
            }

            return $transaction;
        });
    }

    private function updateUserBalance(User $user, Money $amount, TransactionType $type): void
    {
        $balance = $user->balance ?? UserBalance::create(['user_id' => $user->id, 'balance' => 0]);
        $currentMoney = $balance->getMoney();

        if ($type->isDeposit()) {
            $newBalance = $currentMoney->add($amount);
        } else {
            $newBalance = $currentMoney->subtract($amount);
        }

        $balance->updateBalance($newBalance);
    }
} 