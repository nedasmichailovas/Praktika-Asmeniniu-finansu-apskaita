<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kalendorius</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white p-6 rounded shadow mb-6">
            <form method="GET" action="{{ route('calendar.index') }}" class="flex gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">Mėnuo</label>
                    <select name="month" class="border rounded px-3 py-2">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Metai</label>
                    <select name="year" class="border rounded px-3 py-2">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Rodyti</button>
                @php
                    $prevMonth = $month == 1 ? 12 : $month - 1;
                    $prevYear  = $month == 1 ? $year - 1 : $year;
                    $nextMonth = $month == 12 ? 1 : $month + 1;
                    $nextYear  = $month == 12 ? $year + 1 : $year;
                @endphp
                <a href="{{ route('calendar.index', ['month' => $prevMonth, 'year' => $prevYear]) }}" class="bg-gray-100 px-4 py-2 rounded">← Atgal</a>
                <a href="{{ route('calendar.index', ['month' => $nextMonth, 'year' => $nextYear]) }}" class="bg-gray-100 px-4 py-2 rounded">Pirmyn →</a>
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold text-center text-lg mb-4">{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}</h3>

            <div class="grid grid-cols-7 gap-1 mb-2">
                @foreach(['Pr', 'An', 'Tr', 'Kt', 'Pn', 'Š', 'S'] as $day)
                    <div class="text-center text-xs font-semibold text-gray-500 py-2">{{ $day }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-1">
                @for($i = 1; $i < $firstDayOfWeek; $i++)
                    <div class="min-h-20 rounded bg-gray-50"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateKey = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                        $dayTransactions = $transactions->get($dateKey, collect());
                        $dayIncome  = $dayTransactions->where('type', 'income')->sum('amount');
                        $dayExpense = $dayTransactions->where('type', 'expense')->sum('amount');
                        $isToday    = $dateKey === now()->format('Y-m-d');
                    @endphp
                    <div class="min-h-20 rounded border p-1 {{ $isToday ? 'border-blue-400 bg-blue-50' : 'border-gray-100' }}">
                        <div class="text-xs font-semibold {{ $isToday ? 'text-blue-600' : 'text-gray-600' }} mb-1">
                            {{ $day }}
                        </div>
                        @if($dayIncome > 0)
                            <div class="text-xs text-green-600 font-medium truncate">+{{ number_format($dayIncome, 2) }} €</div>
                        @endif
                        @if($dayExpense > 0)
                            <div class="text-xs text-red-600 font-medium truncate">-{{ number_format($dayExpense, 2) }} €</div>
                        @endif
                        @foreach($dayTransactions->take(2) as $t)
                            <div class="text-xs text-gray-400 truncate">{{ $t->category->name }}</div>
                        @endforeach
                        @if($dayTransactions->count() > 2)
                            <div class="text-xs text-gray-400">+{{ $dayTransactions->count() - 2 }} daugiau</div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

    </div>
</x-app-layout>