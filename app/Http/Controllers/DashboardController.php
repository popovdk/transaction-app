<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\UserBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $balance = $user->balance;
        $recentTransactions = $user->transactions()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($transaction) => [
                'id' => $transaction->id,
                'amount' => floatval($transaction->amount),
                'type' => $transaction->type,
                'description' => $transaction->description,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at,
            ]);

        return Inertia::render('Dashboard', [
            'balance' => $balance ? floatval($balance->balance) : 0,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    public function transactions(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $query = $user->transactions();

        // Поиск по описанию
        if ($request->has('search')) {
            $query->where('description', 'ilike', '%' . $request->search . '%');
        }

        // Сортировка по дате
        $sort = $request->input('sort', 'desc');
        $query->orderBy('created_at', $sort);

        $transactions = $query->paginate(10)->through(fn($transaction) => [
            'id' => $transaction->id,
            'amount' => floatval($transaction->amount),
            'type' => $transaction->type,
            'description' => $transaction->description,
            'status' => $transaction->status,
            'created_at' => $transaction->created_at,
        ]);

        return Inertia::render('Transactions', [
            'transactions' => $transactions,
            'search' => $request->input('search', ''),
            'sort' => $sort,
        ]);
    }

    public function getBalance()
    {
        $user = Auth::user();
        $balance = $user->balance;

        return response()->json([
            'balance' => $balance ? floatval($balance->balance) : 0,
        ]);
    }

    public function getRecentTransactions()
    {
        $user = Auth::user();
        $transactions = $user->transactions()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($transaction) => [
                'id' => $transaction->id,
                'amount' => floatval($transaction->amount),
                'type' => $transaction->type,
                'description' => $transaction->description,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at,
            ]);

        return response()->json([
            'transactions' => $transactions,
        ]);
    }
} 