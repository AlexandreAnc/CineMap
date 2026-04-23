<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * On envoie l’utilisateur vers la page de connexion Google.
     */
    public function redirect(): RedirectResponse
    {
        if (config('services.google.client_id') === null || config('services.google.client_id') === '') {
            return redirect()->route('login')->withErrors([
                'google' => 'Google OAuth n’est pas configuré (clés .env vides).',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Google renvoie ici après le choix du compte.
     * On cherche l’utilisateur, ou on le crée, puis on le connecte.
     */
    public function callback(): RedirectResponse
    {
        try {
            $g = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()->route('login')->withErrors([
                'google' => 'Connexion Google annulée ou impossible. Réessaie.',
            ]);
        }

        if ($g->getEmail() === null) {
            return redirect()->route('login')->withErrors([
                'google' => 'Google n’a pas fourni d’e-mail. Impossible de créer le compte.',
            ]);
        }

        $user = User::query()->where('google_id', $g->getId())->first()
            ?? User::query()->where('email', $g->getEmail())->first();

        if ($user) {
            if ($user->google_id === null) {
                $user->google_id = $g->getId();
            }
            $user->name = $g->getName() ?? $g->getNickname() ?? $user->name;
            if ($user->email_verified_at === null) {
                $user->email_verified_at = now();
            }
            $user->save();
        } else {
            $user = User::query()->create([
                'name' => $g->getName() ?? 'Utilisateur',
                'email' => $g->getEmail(),
                'password' => Str::password(32),
                'google_id' => $g->getId(),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended('/dashboard');
    }
}
