<?php

namespace App\Http\Controllers;

use App\Http\Resources\BalanceResource;
use App\Http\Resources\TransactionResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $balance = $user->balance;
        $recentTransactions = $user->transactions()
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'balance' => $balance ? floatval($balance->balance) : 0,
            'recentTransactions' => TransactionResource::collection($recentTransactions),
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

        $transactions = $query->paginate(10);

        return Inertia::render('Transactions', [
            'transactions' => TransactionResource::collection($transactions),
            'search' => $request->input('search', ''),
            'sort' => $sort,
        ]);
    }

    public function getBalance()
    {
        /** @var User $user */
        $user = Auth::user();
        $balance = $user->balance;

        return new BalanceResource($balance);
    }

    public function getRecentTransactions()
    {
        /** @var User $user */
        $user = Auth::user();
        $transactions = $user->transactions()
            ->latest()
            ->take(5)
            ->get();

        return TransactionResource::collection($transactions);
    }
} 