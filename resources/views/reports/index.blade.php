<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ataskaitos</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white p-6 rounded shadow mb-6">
            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap gap-4 items-end">
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
                <a href="{{ route('reports.pdf', request()->query()) }}" class="bg-red-600 text-white px-4 py-2 rounded">
                    Atsisiųsti PDF
                </a>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded shadow text-center">
                <p class="text-gray-500">Pajamos</p>
                <p class="text-2xl font-bold text-green-600">+{{ number_format($totalIncome, 2) }} €</p>
            </div>
            <div class="bg-white p-6 rounded shadow text-center">
                <p class="text-gray-500">Išlaidos</p>
                <p class="text-2xl font-bold text-red-600">-{{ number_format($totalExpense, 2) }} €</p>
            </div>
            <div class="bg-white p-6 rounded shadow text-center">
                <p class="text-gray-500">Likutis</p>
                <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    {{ number_format($balance, 2) }} €
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="font-semibold mb-4">Periodo ataskaita pagal mėnesius</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left p-3">Mėnuo</th>
                        <th class="text-left p-3">Įrašų sk.</th>
                        <th class="text-left p-3 text-green-600">Pajamos</th>
                        <th class="text-left p-3 text-red-600">Išlaidos</th>
                        <th class="text-left p-3 text-blue-600">Likutis</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byMonth as $month => $data)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $month }}</td>
                        <td class="p-3">{{ $data['count'] }}</td>
                        <td class="p-3 text-green-600">+{{ number_format($data['income'], 2) }} €</td>
                        <td class="p-3 text-red-600">-{{ number_format($data['expense'], 2) }} €</td>
                        <td class="p-3 font-semibold {{ $data['balance'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            {{ number_format($data['balance'], 2) }} €
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-3 text-center text-gray-400">Įrašų nėra</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="font-semibold mb-4">Suvestinė pagal kategorijas</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left p-3">Kategorija</th>
                        <th class="text-left p-3">Tipas</th>
                        <th class="text-left p-3">Įrašų sk.</th>
                        <th class="text-left p-3">Min</th>
                        <th class="text-left p-3">Max</th>
                        <th class="text-left p-3">Vidurkis</th>
                        <th class="text-left p-3">Suma</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byCategory as $name => $data)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $name }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs {{ $data['type'] === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $data['type'] === 'income' ? 'Pajamos' : 'Išlaidos' }}
                            </span>
                        </td>
                        <td class="p-3">{{ $data['count'] }}</td>
                        <td class="p-3">{{ number_format($data['min'], 2) }} €</td>
                        <td class="p-3">{{ number_format($data['max'], 2) }} €</td>
                        <td class="p-3">{{ number_format($data['avg'], 2) }} €</td>
                        <td class="p-3 font-semibold">{{ number_format($data['sum'], 2) }} €</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="p-3 text-center text-gray-400">Įrašų nėra</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold mb-4">Visi įrašai</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left p-3">Data</th>
                        <th class="text-left p-3">Aprašymas</th>
                        <th class="text-left p-3">Kategorija</th>
                        <th class="text-left p-3">Tipas</th>
                        <th class="text-left p-3">Suma</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr class="border-t">
                        <td class="p-3">{{ $t->date->format('Y-m-d') }}</td>
                        <td class="p-3">{{ $t->description ?? '-' }}</td>
                        <td class="p-3">{{ $t->category->name }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs {{ $t->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $t->type === 'income' ? 'Pajamos' : 'Išlaidos' }}
                            </span>
                        </td>
                        <td class="p-3 {{ $t->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $t->type === 'income' ? '+' : '-' }}{{ number_format($t->amount, 2) }} €
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-3 text-center text-gray-400">Įrašų nėra</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>