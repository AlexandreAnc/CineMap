<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Paiement</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-sm text-gray-700">
                <p class="text-green-700">Paiement reçu. Ton abonnement pro est enregistré en revenant sur le site (synchronisation automatique, sans webhook requis pour ce flux).</p>
                <p class="mt-2">Tu peux obtenir un token JWT : <code class="text-xs bg-gray-100 px-1 rounded">POST /api/auth/login</code> avec email et mot de passe.</p>
                <a href="{{ route('subscribe') }}" class="mt-4 inline-block text-indigo-600 hover:underline">Retour abonnement</a>
            </div>
        </div>
    </div>
</x-app-layout>
