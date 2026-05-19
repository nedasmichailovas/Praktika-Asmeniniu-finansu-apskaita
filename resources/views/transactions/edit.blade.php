<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Keisti įrašą</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('transactions.update', $transaction) }}">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Tipas</label>
                    <select name="type" class="w-full border rounded px-3 py-2">
                        <option value="income" {{ $transaction->type === 'income' ? 'selected' : '' }}>Pajamos</option>
                        <option value="expense" {{ $transaction->type === 'expense' ? 'selected' : '' }}>Išlaidos</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Kategorija</label>
                    <select name="category_id" class="w-full border rounded px-3 py-2">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $transaction->category_id === $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->type === 'income' ? 'Pajamos' : 'Išlaidos' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Suma (€)</label>
                    <input type="number" step="0.01" name="amount" class="w-full border rounded px-3 py-2" value="{{ old('amount', $transaction->amount) }}">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Data</label>
                    <input type="date" name="date" class="w-full border rounded px-3 py-2" value="{{ old('date', $transaction->date->format('Y-m-d')) }}">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Aprašymas (neprivaloma)</label>
                    <input type="text" name="description" class="w-full border rounded px-3 py-2" value="{{ old('description', $transaction->description) }}">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Atnaujinti</button>
                <a href="{{ route('transactions.index') }}" class="ml-3 text-gray-500">Atšaukti</a>
            </form>
        </div>
    </div>
</x-app-layout>