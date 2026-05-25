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
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
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
                'type'  => $items->first()->type,
            ];
        });

        $byMonth = $transactions->groupBy(function ($t) {
            return $t->date->format('Y-m');
        })->map(function ($items) {
            return [
                'income'  => $items->where('type', 'income')->sum('amount'),
                'expense' => $items->where('type', 'expense')->sum('amount'),
                'balance' => $items->where('type', 'income')->sum('amount') - $items->where('type', 'expense')->sum('amount'),
                'count'   => $items->count(),
            ];
        });

        $categories = auth()->user()->categories()->get();

        return view('reports.index', compact(
            'transactions', 'totalIncome', 'totalExpense', 'balance',
            'byCategory', 'byMonth', 'categories', 'request'
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
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
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
                'type'  => $items->first()->type,
            ];
        });

        $byMonth = $transactions->groupBy(function ($t) {
            return $t->date->format('Y-m');
        })->map(function ($items) {
            return [
                'income'  => $items->where('type', 'income')->sum('amount'),
                'expense' => $items->where('type', 'expense')->sum('amount'),
                'balance' => $items->where('type', 'income')->sum('amount') - $items->where('type', 'expense')->sum('amount'),
                'count'   => $items->count(),
            ];
        });

        $pdf = Pdf::loadView('reports.pdf', compact(
            'transactions', 'totalIncome', 'totalExpense', 'balance',
            'byCategory', 'byMonth', 'request'
        ));

        return $pdf->download('ataskaita.pdf');
    }
    public function sendEmail(Request $request)
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
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
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
            'type'  => $items->first()->type,
        ];
    });

    $byMonth = $transactions->groupBy(function ($t) {
        return $t->date->format('Y-m');
    })->map(function ($items) {
        return [
            'income'  => $items->where('type', 'income')->sum('amount'),
            'expense' => $items->where('type', 'expense')->sum('amount'),
            'balance' => $items->where('type', 'income')->sum('amount') - $items->where('type', 'expense')->sum('amount'),
            'count'   => $items->count(),
        ];
    });

    $pdf = Pdf::loadView('reports.pdf', compact(
        'transactions', 'totalIncome', 'totalExpense', 'balance',
        'byCategory', 'byMonth', 'request'
    ));

    $email = $request->filled('email') ? $request->email : $user->email;

    \Mail::to($email)->send(new \App\Mail\ReportMail($pdf->output()));

    return redirect()->route('reports.index', request()->query())
        ->with('success', 'Ataskaita išsiųsta el. paštu: ' . $email);
}
}