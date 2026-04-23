<?php

namespace App\Http\Controllers;

use App\Services\SyncUserSubscriptionFromCheckoutSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Cashier\Checkout;

class SubscriptionController extends Controller
{
    public function show(): View
    {
        return view('subscribe');
    }

    public function checkout(Request $request): RedirectResponse|Checkout
    {
        $price = config('services.stripe.price_premium');

        if (empty($price)) {
            return back()->with('ok', 'Configure STRIPE_PREMIUM_PRICE dans le .env (ID du prix Stripe, ex. price_…).');
        }

        return $request->user()
            ->newSubscription('default', $price)
            ->checkout([
                'success_url' => route('subscribe.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscribe'),
            ]);
    }

    public function success(Request $request, SyncUserSubscriptionFromCheckoutSession $sync): View
    {
        $sessionId = (string) $request->query('session_id', '');

        if ($sessionId !== '' && $request->user() !== null) {
            $sync($request->user(), $sessionId);
        }

        return view('subscribe-success');
    }
}
