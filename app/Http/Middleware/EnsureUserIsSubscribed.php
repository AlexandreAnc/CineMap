<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isPro()) {
            if (! $request->expectsJson()) {
                return redirect()->route('subscribe')
                    ->with('status', 'Un abonnement premium actif est requis pour accéder à cette page.');
            }

            return response()->json(['message' => 'Un abonnement actif est requis.'], 403);
        }

        return $next($request);
    }
}
