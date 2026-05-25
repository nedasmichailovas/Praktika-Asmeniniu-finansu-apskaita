<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suvestinė</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

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
            <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
                <h3 class="font-semibold">Šis mėnuo vs Pasirinktas mėnuo</h3>
                <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2 items-end">
                    <input type="hidden" name="period" value="{{ $period }}">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Lyginti su mėnesiu</label>
                        <select name="compare_month" class="border rounded px-2 py-1 text-sm">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $compareMonth ? 'selected' : '' }}>{{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Metai</label>
                        <select name="compare_year" class="border rounded px-2 py-1 text-sm">
                            @for($y = now()->year - 3; $y <= now()->year; $y++)
                                <option value="{{ $y }}" {{ $y == $compareYear ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Rodyti</button>
                </form>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="pb-2"></th>
                        <th class="pb-2 text-green-600">Pajamos</th>
                        <th class="pb-2 text-red-600">Išlaidos</th>
                        <th class="pb-2 text-blue-600">Likutis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-2 font-medium">{{ now()->format('Y-m') }}</td>
                        <td class="py-2 text-green-600">+{{ number_format($thisMonthIncome, 2) }} €</td>
                        <td class="py-2 text-red-600">-{{ number_format($thisMonthExpense, 2) }} €</td>
                        <td class="py-2 font-bold {{ ($thisMonthIncome - $thisMonthExpense) >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            {{ number_format($thisMonthIncome - $thisMonthExpense, 2) }} €
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 font-medium">{{ $compareYear }}-{{ str_pad($compareMonth, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-2 text-green-600">+{{ number_format($lastMonthIncome, 2) }} €</td>
                        <td class="py-2 text-red-600">-{{ number_format($lastMonthExpense, 2) }} €</td>
                        <td class="py-2 font-bold {{ ($lastMonthIncome - $lastMonthExpense) >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            {{ number_format($lastMonthIncome - $lastMonthExpense, 2) }} €
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white p-6 rounded shadow mb-6">
            <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
                <h3 class="font-semibold">Balanso kitimas</h3>
                <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
                    <input type="hidden" name="compare_month" value="{{ $compareMonth }}">
                    <input type="hidden" name="compare_year" value="{{ $compareYear }}">
                    @foreach(['1month' => 'Šis mėnuo', '3months' => '3 mėn.', '6months' => '6 mėn.', '1year' => 'Metai'] as $val => $label)
                        <button type="submit" name="period" value="{{ $val }}"
                            class="px-3 py-1 rounded text-sm {{ $period === $val ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </form>
            </div>
            <canvas id="lineChart"></canvas>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold mb-4">Išlaidos pagal kategorijas</h3>
                <canvas id="pieChart"></canvas>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold">Paskutiniai įrašai</h3>
                    <a href="{{ route('transactions.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">+ Naujas</a>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="pb-2">Data</th>
                            <th class="pb-2">Kategorija</th>
                            <th class="pb-2">Suma</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $t)
                        <tr class="border-b">
                            <td class="py-2">{{ $t->date->format('Y-m-d') }}</td>
                            <td class="py-2">{{ $t->category->name }}</td>
                            <td class="py-2 {{ $t->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $t->type === 'income' ? '+' : '-' }}{{ number_format($t->amount, 2) }} €
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">Įrašų nėra</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($recentTransactions->where('type','expense')->pluck('category.name')->values()) !!},
                datasets: [{
                    data: {!! json_encode($recentTransactions->where('type','expense')->pluck('amount')->values()) !!},
                    backgroundColor: ['#3b82f6','#f59e0b','#ef4444','#8b5cf6','#10b981'],
                }]
            }
        });

        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($lineLabels) !!},
                datasets: [{
                    label: 'Likutis',
                    data: {!! json_encode($lineBalance) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 2,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks: {
                            maxTicksLimit: 10,
                            maxRotation: 45,
                        }
                    },
                    y: { beginAtZero: false }
                }
            }
        });
    </script>
</x-app-layout>