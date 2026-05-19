<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nauja kategorija</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Pavadinimas</label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name') }}">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Tipas</label>
                    <select name="type" class="w-full border rounded px-3 py-2">
                        <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Pajamos</option>
                        <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Išlaidos</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Išsaugoti</button>
                <a href="{{ route('categories.index') }}" class="ml-3 text-gray-500">Atšaukti</a>
            </form>
        </div>
    </div>
</x-app-layout>