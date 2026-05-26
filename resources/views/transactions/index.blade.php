<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Įrašai</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white p-6 rounded shadow mb-6">
            <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">Paieška</label>
                    <input type="text" name="search" class="border rounded px-3 py-2 w-48" placeholder="Aprašymas..." value="{{ request('search') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nuo</label>
                    <input type="date" name="date_from" class="border rounded px-3 py-2" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Iki</label>
                    <input type="date" name="date_to" class="border rounded px-3 py-2" value="{{ request('date_to') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tipas</label>
                    <select name="type" class="border rounded px-3 py-2">
                        <option value="">Visi</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pajamos</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Išlaidos</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Kategorija</label>
                    <select name="category_id" class="border rounded px-3 py-2">
                        <option value="">Visos</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filtruoti</button>
                <a href="{{ route('transactions.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded">Išvalyti</a>
            </form>
        </div>

        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('transactions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                + Naujas įrašas
            </a>
            <p class="text-sm text-gray-500">Rasta įrašų: {{ $transactions->total() }}</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left p-4">Data</th>
                        <th class="text-left p-4">Aprašymas</th>
                        <th class="text-left p-4">Kategorija</th>
                        <th class="text-left p-4">Tipas</th>
                        <th class="text-left p-4">Suma</th>
                        <th class="text-left p-4">Veiksmai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr class="border-t">
                        <td class="p-4">{{ $t->date->format('Y-m-d') }}</td>
                        <td class="p-4">{{ $t->description ?? '-' }}</td>
                        <td class="p-4">{{ $t->category->name }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs {{ $t->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $t->type === 'income' ? 'Pajamos' : 'Išlaidos' }}
                            </span>
                        </td>
                        <td class="p-4 {{ $t->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $t->type === 'income' ? '+' : '-' }}{{ number_format($t->amount, 2) }} €
                        </td>
                        <td class="p-4 flex gap-2">
                            <a href="{{ route('transactions.edit', $t) }}" class="text-blue-600 hover:underline">Keisti</a>
                            <form action="{{ route('transactions.destroy', $t) }}" method="POST" onsubmit="return confirm('Ar tikrai?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Trinti</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-4 text-center text-gray-400">Įrašų nėra</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->appends(request()->query())->links() }}
        </div>

    </div>
</x-app-layout>