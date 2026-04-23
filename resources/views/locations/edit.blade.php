<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier l'emplacement</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($films->isEmpty())
                    <p class="text-gray-600">Aucun film en base.</p>
                @else
                    <form method="post" action="{{ route('locations.update', $location) }}" class="space-y-4" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="film_id" value="Film" />
                            <select id="film_id" name="film_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach ($films as $f)
                                    <option value="{{ $f->id }}" @selected(old('film_id', $location->film_id) == $f->id)>{{ $f->title }} ({{ $f->release_year }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('film_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="name" value="Nom du lieu" />
                            <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $location->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="city" value="Ville" />
                            <x-text-input id="city" name="city" type="text" class="block mt-1 w-full" :value="old('city', $location->city)" required />
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="country" value="Pays" />
                            <x-text-input id="country" name="country" type="text" class="block mt-1 w-full" :value="old('country', $location->country)" required />
                            <x-input-error :messages="$errors->get('country')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $location->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="photo" value="Nouvelle photo du lieu (optionnel)" />
                            <input id="photo" name="photo" type="file" accept="image/*" class="block mt-1 w-full text-sm">
                            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                            @if ($location->photo_path)
                                <img src="{{ asset('storage/'.$location->photo_path) }}" alt="Photo actuelle du lieu" class="mt-3 h-32 w-auto rounded border border-gray-200">
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <x-primary-button>Mettre à jour</x-primary-button>
                            <a href="{{ route('locations.show', $location) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase">Annuler</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
