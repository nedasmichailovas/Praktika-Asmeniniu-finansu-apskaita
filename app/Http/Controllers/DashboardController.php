<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalIncome  = $user->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $user->transactions()->where('type', 'expense')->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        $recentTransactions = $user->transactions()->with('category')->latest()->take(5)->get();

        $thisMonthIncome  = $user->transactions()->where('type', 'income')->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount');
        $thisMonthExpense = $user->transactions()->where('type', 'expense')->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount');

        $lastMonthIncome  = $user->transactions()->where('type', 'income')->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year)->sum('amount');
        $lastMonthExpense = $user->transactions()->where('type', 'expense')->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year)->sum('amount');

        $lineLabels  = [];
        $lineBalance = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $lineLabels[] = $month->format('Y-m');
            $inc = $user->transactions()->where('type', 'income')->whereMonth('date', $month->month)->whereYear('date', $month->year)->sum('amount');
            $exp = $user->transactions()->where('type', 'expense')->whereMonth('date', $month->month)->whereYear('date', $month->year)->sum('amount');
            $lineBalance[] = round($inc - $exp, 2);
        }

        return view('dashboard', compact(
            'totalIncome', 'totalExpense', 'balance', 'recentTransactions',
            'thisMonthIncome', 'thisMonthExpense',
            'lastMonthIncome', 'lastMonthExpense',
            'lineLabels', 'lineBalance'
        ));
    }
}