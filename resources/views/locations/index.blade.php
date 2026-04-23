<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Emplacements</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex justify-end">
                <a href="{{ route('locations.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Nouvel emplacement</a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="get" action="{{ route('locations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Filtrer par ville</label>
                        <input id="city" name="city" type="text" value="{{ $filters['city'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Ex: Lyon">
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Filtrer par pays</label>
                        <input id="country" name="country" type="text" value="{{ $filters['country'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Ex: France">
                    </div>
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700">Trier</label>
                        <select id="sort" name="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="recent" @selected($filters['sort'] === 'recent')>Date de création (récent)</option>
                            <option value="most_upvoted" @selected($filters['sort'] === 'most_upvoted')>Plus upvotés</option>
                            <option value="least_upvoted" @selected($filters['sort'] === 'least_upvoted')>Moins upvotés</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            Filtrer
                        </button>
                        <a href="{{ route('locations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    @if ($locations->isEmpty())
                        <p class="text-gray-600">Aucun emplacement pour le moment.</p>
                    @else
                        <table class="min-w-full text-sm text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 pr-4">Photo</th>
                                    <th class="py-2 pr-4">Lieu</th>
                                    <th class="py-2 pr-4">Film</th>
                                    <th class="py-2 pr-4">Ville</th>
                                    <th class="py-2 pr-4">Pays</th>
                                    <th class="py-2 pr-4">Créé par</th>
                                    <th class="py-2 pr-4">Votes</th>
                                    <th class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($locations as $location)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 pr-4">
                                            @if ($location->photo_path)
                                                <img src="{{ asset('storage/'.$location->photo_path) }}" alt="Photo de {{ $location->name }}" class="h-12 w-12 object-cover rounded border border-gray-200">
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="py-2 pr-4">{{ $location->name }}</td>
                                        <td class="py-2 pr-4">{{ $location->film->title }}</td>
                                        <td class="py-2 pr-4">{{ $location->city }}</td>
                                        <td class="py-2 pr-4">{{ $location->country }}</td>
                                        <td class="py-2 pr-4">{{ $location->user->name }}</td>
                                        <td class="py-2 pr-4">{{ $location->upvotes_count }}</td>
                                        <td class="py-2 space-x-2 whitespace-nowrap">
                                            <a href="{{ route('locations.show', $location) }}" class="text-indigo-600 hover:underline">Voir</a>
                                            @if (auth()->user()->is_admin || auth()->id() === $location->user_id)
                                                <a href="{{ route('locations.edit', $location) }}" class="text-indigo-600 hover:underline">Modifier</a>
                                                <form action="{{ route('locations.destroy', $location) }}" method="post" class="inline" onsubmit="return confirm('Supprimer cet emplacement ?');">
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
