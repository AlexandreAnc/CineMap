<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Films</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (auth()->user()->is_admin)
                <div class="flex justify-end">
                    <a href="{{ route('films.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Nouveau film</a>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    @if ($films->isEmpty())
                        <p class="text-gray-600">Aucun film pour le moment.</p>
                    @else
                        <table class="min-w-full text-sm text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 pr-4">Titre</th>
                                    <th class="py-2 pr-4">Année</th>
                                    <th class="py-2 pr-4">Emplacements</th>
                                    <th class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($films as $film)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 pr-4">{{ $film->title }}</td>
                                        <td class="py-2 pr-4">{{ $film->release_year }}</td>
                                        <td class="py-2 pr-4">{{ $film->locations_count }}</td>
                                        <td class="py-2 space-x-2 whitespace-nowrap">
                                            <a href="{{ route('films.show', $film) }}" class="text-indigo-600 hover:underline">Voir</a>
                                            @if (auth()->user()->is_admin)
                                                <a href="{{ route('films.edit', $film) }}" class="text-indigo-600 hover:underline">Modifier</a>
                                                <form action="{{ route('films.destroy', $film) }}" method="post" class="inline" onsubmit="return confirm('Supprimer ce film ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
