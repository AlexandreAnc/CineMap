<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouveau film</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="post" action="{{ route('films.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="title" value="Titre" />
                        <x-text-input id="title" name="title" type="text" class="block mt-1 w-full" :value="old('title')" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="release_year" value="Année de sortie" />
                        <x-text-input id="release_year" name="release_year" type="number" class="block mt-1 w-full" :value="old('release_year')" required />
                        <x-input-error :messages="$errors->get('release_year')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="synopsis" value="Synopsis" />
                        <textarea id="synopsis" name="synopsis" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('synopsis') }}</textarea>
                        <x-input-error :messages="$errors->get('synopsis')" class="mt-2" />
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button>Enregistrer</x-primary-button>
                        <a href="{{ route('films.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
