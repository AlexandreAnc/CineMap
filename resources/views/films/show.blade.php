<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $film->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('films.index') }}" class="text-sm text-indigo-600 hover:underline">← Liste des films</a>
                @if (auth()->user()->is_admin)
                    <a href="{{ route('films.edit', $film) }}" class="text-sm text-indigo-600 hover:underline">Modifier</a>
                    <form action="{{ route('films.destroy', $film) }}" method="post" class="inline" onsubmit="return confirm('Supprimer ce film ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:underline">Supprimer</button>
                    </form>
                @endif
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-2">
                <p><span class="font-medium">Année :</span> {{ $film->release_year }}</p>
                <p><span class="font-medium">Synopsis :</span></p>
                <p class="text-gray-700 whitespace-pre-line">{{ $film->synopsis ?: '—' }}</p>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-3">Emplacements liés</h3>
                <form method="get" action="{{ route('films.show', $film) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end mb-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Filtrer par ville</label>
                        <input id="city" name="city" type="text" value="{{ $filters['city'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Ex: Lyon">
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Filtrer par pays</label>
                        <input id="country" name="country" type="text" value="{{ $filters['country'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Ex: France">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            Filtrer
                        </button>
                        <a href="{{ route('films.show', $film) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Réinitialiser
                        </a>
                    </div>
                </form>
                @if ($film->locations->isEmpty())
                    <p class="text-gray-600">Aucun emplacement pour ce film.</p>
                @else
                    <ul class="space-y-2 text-sm">
                        @foreach ($film->locations as $loc)
                            <li>
                                @if ($loc->photo_path)
                                    <img src="{{ asset('storage/'.$loc->photo_path) }}" alt="Photo de {{ $loc->name }}" class="inline-block mr-2 h-10 w-10 object-cover rounded border border-gray-200 align-middle">
                                @endif
                                <a href="{{ route('locations.show', $loc) }}" class="text-indigo-600 hover:underline">{{ $loc->name }}</a>
                                <span class="text-gray-500">({{ $loc->city }}, par {{ $loc->user->name }})</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
