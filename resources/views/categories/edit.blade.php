<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Keisti kategoriją</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('categories.update', $category) }}">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Pavadinimas</label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name', $category->name) }}">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Tipas</label>
                    <select name="type" class="w-full border rounded px-3 py-2">
                        <option value="income" {{ $category->type === 'income' ? 'selected' : '' }}>Pajamos</option>
                        <option value="expense" {{ $category->type === 'expense' ? 'selected' : '' }}>Išlaidos</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Atnaujinti</button>
                <a href="{{ route('categories.index') }}" class="ml-3 text-gray-500">Atšaukti</a>
            </form>
        </div>
    </div>
</x-app-layout>