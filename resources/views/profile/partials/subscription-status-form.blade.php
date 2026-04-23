@php
    $hasActive = $user->isPro();
@endphp
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Abonnement') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Accès API aux emplacements des films (abonnement Stripe).') }}
        </p>
    </header>

    <div class="mt-6">
        @if ($hasActive)
            <p class="inline-flex items-center rounded-md bg-green-50 px-3 py-1 text-sm font-medium text-green-800 ring-1 ring-inset ring-green-600/20">
                {{ __('Abonnement actif') }}
            </p>
        @else
            <p class="text-sm text-gray-600">
                {{ __('Aucun abonnement actif.') }}
            </p>
            <p class="mt-3">
                <a
                    class="text-sm text-indigo-600 underline decoration-indigo-500 hover:text-indigo-800"
                    href="{{ route('subscribe') }}"
                >{{ __('Voir les offres / souscrire') }}</a>
            </p>
        @endif
    </div>
</section>
