<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Naujas įrašas</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('transactions.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Tipas</label>
                    <select name="type" id="type" class="w-full border rounded px-3 py-2" onchange="filterCategories()">
                        <option value="income">Pajamos</option>
                        <option value="expense">Išlaidos</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Kategorija</label>
                    <select name="category_id" id="category_id" class="w-full border rounded px-3 py-2">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-type="{{ $category->type }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Suma (€)</label>
                    <input type="number" step="0.01" name="amount" class="w-full border rounded px-3 py-2" value="{{ old('amount') }}">
                    @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Data</label>
                    <input type="date" name="date" class="w-full border rounded px-3 py-2" value="{{ old('date', date('Y-m-d')) }}">
                    @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Aprašymas (neprivaloma)</label>
                    <input type="text" name="description" class="w-full border rounded px-3 py-2" value="{{ old('description') }}">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Išsaugoti</button>
                <a href="{{ route('transactions.index') }}" class="ml-3 text-gray-500">Atšaukti</a>
            </form>
        </div>
    </div>

    <script>
    function filterCategories() {
        const type = document.getElementById('type').value;
        const options = document.getElementById('category_id').options;
        for (let i = 0; i < options.length; i++) {
            options[i].style.display = options[i].dataset.type === type ? '' : 'none';
        }
        for (let i = 0; i < options.length; i++) {
            if (options[i].dataset.type === type) {
                document.getElementById('category_id').value = options[i].value;
                break;
            }
        }
    }
    document.addEventListener('DOMContentLoaded', filterCategories);
    </script>
</x-app-layout>