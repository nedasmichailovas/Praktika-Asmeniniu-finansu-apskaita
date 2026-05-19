<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kategorijos</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
            + Nauja kategorija
        </a>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left p-4">Pavadinimas</th>
                        <th class="text-left p-4">Tipas</th>
                        <th class="text-left p-4">Veiksmai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr class="border-t">
                        <td class="p-4">{{ $category->name }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs {{ $category->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $category->type === 'income' ? 'Pajamos' : 'Išlaidos' }}
                            </span>
                        </td>
                        <td class="p-4 flex gap-2">
                            <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 hover:underline">Keisti</a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Ar tikrai?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Trinti</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="p-4 text-center text-gray-400">Kategorijų nėra</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>