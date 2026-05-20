<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);
        $user  = auth()->user();

        $budgets = $user->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->map(function ($budget) use ($month, $year) {
                $spent = auth()->user()->transactions()
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->sum('amount');

                $budget->spent      = $spent;
                $budget->remaining  = $budget->amount - $spent;
                $budget->percent    = $budget->amount > 0 ? min(100, round(($spent / $budget->amount) * 100)) : 0;
                return $budget;
            });

        $categories = $user->categories()->where('type', 'expense')->get();

        return view('budgets.index', compact('budgets', 'categories', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0.01',
            'month'       => 'required|integer|between:1,12',
            'year'        => 'required|integer|min:2000',
        ]);

        Budget::updateOrCreate(
            [
                'user_id'     => auth()->id(),
                'category_id' => $request->category_id,
                'month'       => $request->month,
                'year'        => $request->year,
            ],
            ['amount' => $request->amount]
        );

        return redirect()->route('budgets.index', ['month' => $request->month, 'year' => $request->year])
            ->with('success', 'Biudžetas išsaugotas!');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return back()->with('success', 'Biudžetas ištrintas!');
    }
}