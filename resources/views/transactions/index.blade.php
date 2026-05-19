<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Įrašai</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('transactions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
            + Naujas įrašas
        </a>

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
    </div>
</x-app-layout>