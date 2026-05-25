<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 5px; }
        p.subtitle { text-align: center; color: #666; margin-bottom: 20px; font-size: 10px; }
        .summary { width: 100%; margin-bottom: 20px; }
        .summary td { padding: 10px; text-align: center; border: 1px solid #ddd; }
        .label { font-size: 10px; color: #666; }
        .amount { font-size: 14px; font-weight: bold; }
        .green { color: #16a34a; }
        .red { color: #dc2626; }
        .blue { color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; text-align: left; padding: 6px 8px; font-size: 10px; border-bottom: 2px solid #ddd; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 10px; }
        h2 { font-size: 12px; margin-bottom: 6px; margin-top: 16px; padding-bottom: 4px; border-bottom: 1px solid #ddd; }
        .badge-income { background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 3px; }
        .badge-expense { background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Finansų ataskaita</h1>
    <p class="subtitle">
        Sugeneruota: {{ now()->format('Y-m-d H:i') }}
        @if(request('date_from') || request('date_to'))
            | Periodas: {{ request('date_from', '...') }} – {{ request('date_to', '...') }}
        @endif
    </p>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Pajamos</div>
                <div class="amount green">+{{ number_format($totalIncome, 2) }} €</div>
            </td>
            <td>
                <div class="label">Išlaidos</div>
                <div class="amount red">-{{ number_format($totalExpense, 2) }} €</div>
            </td>
            <td>
                <div class="label">Likutis</div>
                <div class="amount blue">{{ number_format($balance, 2) }} €</div>
            </td>
        </tr>
    </table>

    <h2>Periodo ataskaita pagal mėnesius</h2>
    <table>
        <thead>
            <tr>
                <th>Mėnuo</th>
                <th>Įrašų sk.</th>
                <th>Pajamos</th>
                <th>Išlaidos</th>
                <th>Likutis</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byMonth as $month => $data)
            <tr>
                <td>{{ $month }}</td>
                <td>{{ $data['count'] }}</td>
                <td class="green">+{{ number_format($data['income'], 2) }} €</td>
                <td class="red">-{{ number_format($data['expense'], 2) }} €</td>
                <td class="{{ $data['balance'] >= 0 ? 'blue' : 'red' }}">{{ number_format($data['balance'], 2) }} €</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Suvestinė pagal kategorijas</h2>
    <table>
        <thead>
            <tr>
                <th>Kategorija</th>
                <th>Tipas</th>
                <th>Įrašų sk.</th>
                <th>Min</th>
                <th>Max</th>
                <th>Vidurkis</th>
                <th>Suma</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byCategory as $name => $data)
            <tr>
                <td>{{ $name }}</td>
                <td>
                    <span class="{{ $data['type'] === 'income' ? 'badge-income' : 'badge-expense' }}">
                        {{ $data['type'] === 'income' ? 'Pajamos' : 'Išlaidos' }}
                    </span>
                </td>
                <td>{{ $data['count'] }}</td>
                <td>{{ number_format($data['min'], 2) }} €</td>
                <td>{{ number_format($data['max'], 2) }} €</td>
                <td>{{ number_format($data['avg'], 2) }} €</td>
                <td><strong>{{ number_format($data['sum'], 2) }} €</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Visi įrašai</h2>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Aprašymas</th>
                <th>Kategorija</th>
                <th>Tipas</th>
                <th>Suma</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->date->format('Y-m-d') }}</td>
                <td>{{ $t->description ?? '-' }}</td>
                <td>{{ $t->category->name }}</td>
                <td>
                    <span class="{{ $t->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                        {{ $t->type === 'income' ? 'Pajamos' : 'Išlaidos' }}
                    </span>
                </td>
                <td class="{{ $t->type === 'income' ? 'green' : 'red' }}">
                    {{ $t->type === 'income' ? '+' : '-' }}{{ number_format($t->amount, 2) }} €
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>