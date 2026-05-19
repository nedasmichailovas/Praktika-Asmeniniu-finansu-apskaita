<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalIncome  = $user->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $user->transactions()->where('type', 'expense')->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        $recentTransactions = $user->transactions()->with('category')->latest()->take(5)->get();

        return view('dashboard', compact('totalIncome', 'totalExpense', 'balance', 'recentTransactions'));
    }
}