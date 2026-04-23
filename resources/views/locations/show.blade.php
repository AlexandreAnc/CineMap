<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $location->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('locations.index') }}" class="text-sm text-indigo-600 hover:underline">← Liste</a>
                <a href="{{ route('films.show', $location->film) }}" class="text-sm text-indigo-600 hover:underline">Film : {{ $location->film->title }}</a>
                @if (auth()->user()->is_admin || auth()->id() === $location->user_id)
                    <a href="{{ route('locations.edit', $location) }}" class="text-sm text-indigo-600 hover:underline">Modifier</a>
                    <form action="{{ route('locations.destroy', $location) }}" method="post" class="inline" onsubmit="return confirm('Supprimer cet emplacement ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:underline">Supprimer</button>
                    </form>
                @endif
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-2 text-sm">
                @if ($location->photo_path)
                    <img src="{{ asset('storage/'.$location->photo_path) }}" alt="Photo du lieu {{ $location->name }}" class="mb-4 max-h-72 w-auto rounded border border-gray-200">
                @endif
                <p><span class="font-medium">Ville :</span> {{ $location->city }}</p>
                <p><span class="font-medium">Pays :</span> {{ $location->country }}</p>
                <p><span class="font-medium">Upvotes :</span> <span id="upvotes-value">{{ $location->upvotes_count }}</span></p>
                <p><span class="font-medium">Créé par :</span> {{ $location->user->name }}</p>
                <p><span class="font-medium">Description :</span></p>
                <p class="text-gray-700 whitespace-pre-line">{{ $location->description ?: '—' }}</p>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" id="vote-box">
                <p id="vote-hint" class="text-sm text-gray-600 mb-2">{{ $hasVoted ? 'Tu as voté pour ce lieu.' : 'Tu n’as pas encore voté.' }}</p>
                <button
                    type="button"
                    id="vote-btn"
                    data-url="{{ route('locations.vote', $location) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                >{{ $hasVoted ? 'Retirer mon vote' : 'Upvote' }}</button>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const btn = document.getElementById('vote-btn');
            const valueEl = document.getElementById('upvotes-value');
            const hint = document.getElementById('vote-hint');
            if (!btn || !valueEl) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            btn.addEventListener('click', function () {
                const fd = new FormData();
                fd.append('_token', token);
                fetch(btn.dataset.url, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        valueEl.textContent = data.upvotes_count;
                        btn.textContent = data.has_voted ? 'Retirer mon vote' : 'Upvote';
                        if (hint) {
                            hint.textContent = data.has_voted
                                ? 'Tu as voté pour ce lieu.'
                                : 'Tu n’as pas encore voté.';
                        }
                    })
                    .catch(function () {
                        window.location.reload();
                    });
            });
        })();
    </script>
</x-app-layout>
