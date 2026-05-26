<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Biudžetas</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="flex gap-4 mb-6 items-end">
            <form method="GET" action="{{ route('budgets.index') }}" class="flex gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">Mėnuo</label>
                    <select name="month" class="border rounded px-3 py-2 w-14">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Metai</label>
                    <select name="year" class="border rounded px-3 py-2 w-20">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Rodyti</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold mb-4">Naujas biudžetas</h3>
                <form method="POST" action="{{ route('budgets.store') }}">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Kategorija</label>
                        <select name="category_id" class="w-full border rounded px-3 py-2">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Limitas (€)</label>
                        <input type="number" step="0.01" name="amount" class="w-full border rounded px-3 py-2">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Išsaugoti</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold mb-4">{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }} biudžetai</h3>
                @forelse($budgets as $budget)
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium">{{ $budget->category->name }}</span>
                        <div class="flex items-center gap-3">
                            <span class="text-sm {{ $budget->remaining < 0 ? 'text-red-600' : 'text-gray-600' }}">
                                {{ number_format($budget->spent, 2) }} / {{ number_format($budget->amount, 2) }} €
                            </span>
                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Ar tikrai?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 text-xs hover:underline">Trinti</button>
                            </form>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full {{ $budget->percent >= 100 ? 'bg-red-500' : ($budget->percent >= 75 ? 'bg-yellow-500' : 'bg-green-500') }}"
                            style="width: {{ $budget->percent }}%"></div>
                    </div>
                    <p class="text-xs mt-1 {{ $budget->remaining < 0 ? 'text-red-600' : 'text-gray-400' }}">
                        {{ $budget->remaining < 0 ? 'Viršyta: ' : 'Liko: ' }}{{ number_format(abs($budget->remaining), 2) }} €
                    </p>
                </div>
                @empty
                <p class="text-gray-400 text-sm">Biudžetų nėra</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>