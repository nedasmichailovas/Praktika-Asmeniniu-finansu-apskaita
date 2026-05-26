<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
{
    $user  = auth()->user();
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
    if ($request->filled('search')) {
    $query->where(function($q) use ($request) {
        $q->where('description', 'like', '%' . $request->search . '%')
          ->orWhereHas('category', function($q) use ($request) {
              $q->where('name', 'like', '%' . $request->search . '%');
          });
    });
}

    $transactions = $query->latest('date')->paginate(15);
    $categories   = $user->categories()->get();

    return view('transactions.index', compact('transactions', 'categories'));
}

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'date'        => 'required|date',
            'type'        => 'required|in:income,expense',
        ]);

        auth()->user()->transactions()->create($request->all());

        return redirect()->route('transactions.index')->with('success', 'Įrašas pridėtas!');
    }

    public function create()
    {
        $categories = auth()->user()->categories()->get();
        return view('transactions.create', compact('categories'));
    }

    public function edit(Transaction $transaction)
    {
        $categories = auth()->user()->categories()->get();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'date'        => 'required|date',
            'type'        => 'required|in:income,expense',
        ]);

        $transaction->update($request->all());

        return redirect()->route('transactions.index')->with('success', 'Įrašas atnaujintas!');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Įrašas ištrintas!');
    }
}