<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nustatymai</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('settings.update') }}">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Valiuta</label>
                    <input type="text" name="currency" class="w-full border rounded px-3 py-2" value="{{ $settings['currency'] ?? '€' }}">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Datos formatas</label>
                    <select name="date_format" class="w-full border rounded px-3 py-2">
                        <option value="Y-m-d" {{ ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' }}>2025-01-31</option>
                        <option value="d/m/Y" {{ ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' }}>31/01/2025</option>
                        <option value="d.m.Y" {{ ($settings['date_format'] ?? '') === 'd.m.Y' ? 'selected' : '' }}>31.01.2025</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Išsaugoti</button>
            </form>
        </div>
    </div>
</x-app-layout>