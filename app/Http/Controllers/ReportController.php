<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = $user->transactions()->with('category');

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest('date')->get();

        $totalIncome  = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        $byCategory = $transactions->groupBy('category.name')->map(function ($items) {
            return [
                'sum'   => $items->sum('amount'),
                'count' => $items->count(),
                'min'   => $items->min('amount'),
                'max'   => $items->max('amount'),
                'avg'   => $items->avg('amount'),
            ];
        });

        return view('reports.index', compact(
            'transactions', 'totalIncome', 'totalExpense', 'balance', 'byCategory', 'request'
        ));
    }

    public function pdf(Request $request)
    {
        $user = auth()->user();

        $query = $user->transactions()->with('category');

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest('date')->get();

        $totalIncome  = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        $byCategory = $transactions->groupBy('category.name')->map(function ($items) {
            return [
                'sum'   => $items->sum('amount'),
                'count' => $items->count(),
                'min'   => $items->min('amount'),
                'max'   => $items->max('amount'),
                'avg'   => $items->avg('amount'),
            ];
        });

        $pdf = Pdf::loadView('reports.pdf', compact(
            'transactions', 'totalIncome', 'totalExpense', 'balance', 'byCategory', 'request'
        ));

        return $pdf->download('ataskaita.pdf');
    }
}