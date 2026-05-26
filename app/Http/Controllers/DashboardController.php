<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $selectedMonth = $request->get('selected_month', now()->month);
        $selectedYear  = $request->get('selected_year', now()->year);

        $totalIncome  = $user->transactions()->where('type', 'income')->whereMonth('date', $selectedMonth)->whereYear('date', $selectedYear)->sum('amount');
        $totalExpense = $user->transactions()->where('type', 'expense')->whereMonth('date', $selectedMonth)->whereYear('date', $selectedYear)->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        $recentTransactions = $user->transactions()->with('category')->whereMonth('date', $selectedMonth)->whereYear('date', $selectedYear)->latest()->take(5)->get();

        $thisMonthIncome  = $user->transactions()->where('type', 'income')->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount');
        $thisMonthExpense = $user->transactions()->where('type', 'expense')->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount');

        $compareMonth = $request->get('compare_month', now()->subMonth()->month);
        $compareYear  = $request->get('compare_year', now()->subMonth()->year);

        $lastMonthIncome  = $user->transactions()->where('type', 'income')->whereMonth('date', $compareMonth)->whereYear('date', $compareYear)->sum('amount');
        $lastMonthExpense = $user->transactions()->where('type', 'expense')->whereMonth('date', $compareMonth)->whereYear('date', $compareYear)->sum('amount');

        $period = $request->get('period', '6months');

        if ($period === '1month') {
            $dateFrom = now()->startOfMonth();
            $dateTo   = now()->endOfMonth();
        } elseif ($period === '3months') {
            $dateFrom = now()->subMonths(3)->startOfDay();
            $dateTo   = now()->endOfDay();
        } elseif ($period === '1year') {
            $dateFrom = now()->subYear()->startOfDay();
            $dateTo   = now()->endOfDay();
        } else {
            $dateFrom = now()->subMonths(6)->startOfDay();
            $dateTo   = now()->endOfDay();
        }

        $lineTransactions = $user->transactions()->whereBetween('date', [$dateFrom, $dateTo])->orderBy('date')->get();

        $lineLabels  = [];
        $lineBalance = [];
        $runningBalance = 0;

        $grouped = $lineTransactions->groupBy(function ($t) {
            return $t->date->format('Y-m-d');
        });

        $current = $dateFrom->copy();
        while ($current <= $dateTo) {
            $dateKey = $current->format('Y-m-d');
            $lineLabels[] = $dateKey;
            $dayTransactions = $grouped->get($dateKey, collect());
            $dayIncome  = $dayTransactions->where('type', 'income')->sum('amount');
            $dayExpense = $dayTransactions->where('type', 'expense')->sum('amount');
            $runningBalance += $dayIncome - $dayExpense;
            $lineBalance[] = round($runningBalance, 2);
            $current->addDay();
        }

        $expenseByCategory = $user->transactions()
            ->with('category')
            ->where('type', 'expense')
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear)
            ->get()
            ->groupBy('category.name')
            ->map(fn($items) => $items->sum('amount'));

        return view('dashboard', compact(
            'totalIncome', 'totalExpense', 'balance', 'recentTransactions',
            'thisMonthIncome', 'thisMonthExpense',
            'lastMonthIncome', 'lastMonthExpense',
            'lineLabels', 'lineBalance',
            'compareMonth', 'compareYear', 'period',
            'selectedMonth', 'selectedYear',
            'expenseByCategory'
        ));
    }
}