<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Abonnement CineMap</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4 text-sm text-gray-700">
                <p>Abonnement de test (Stripe). Carte : 4242 4242 4242 4242, une date future, n’importe quel CVC.</p>
                @if (auth()->user()->subscribed('default'))
                    <p class="text-green-700 font-medium">Tu as un abonnement actif.</p>
                @else
                    <form method="post" action="{{ route('subscribe.checkout') }}">
                        @csrf
                        <x-primary-button>Payer l’abonnement (Checkout Stripe)</x-primary-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
