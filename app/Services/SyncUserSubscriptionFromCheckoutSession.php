<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Laravel\Cashier\Cashier;
use Stripe\StripeClient;
use Stripe\Subscription as StripeSubscription;


final class SyncUserSubscriptionFromCheckoutSession
{
    public function __invoke(User $user, string $sessionId, ?StripeClient $stripe = null): void
    {
        if ($sessionId === '' || $user->stripeId() === null) {
            return;
        }

        $stripe = $stripe ?? Cashier::stripe();

        $session = $stripe->checkout->sessions->retrieve(
            $sessionId,
            ['expand' => ['subscription']]
        );

        $customer = is_string($session->customer)
            ? $session->customer
            : $session->customer?->id;

        if ($customer === null || $customer !== $user->stripeId()) {
            return;
        }

        if ($session->mode !== 'subscription' || (string) $session->status !== 'complete') {
            return;
        }

        if ($session->subscription === null) {
            return;
        }

        $subRef = $session->subscription;
        $subscriptionId = is_string($subRef) ? $subRef : $subRef->id;

        $data = $stripe->subscriptions
            ->retrieve($subscriptionId, ['expand' => ['items.data']])
            ->toArray();

        self::applyCustomerSubscriptionData($user, $data);
    }

    /**
     * Même idée que HandleCustomerSubscriptionUpdated dans
     * Laravel\Cashier\Http\Controllers\WebhookController.
     */
    public static function applyCustomerSubscriptionData(User $user, array $data): void
    {
        $subscription = $user->subscriptions()->firstOrNew(['stripe_id' => $data['id']]);

        if (
            isset($data['status'])
            && $data['status'] === StripeSubscription::STATUS_INCOMPLETE_EXPIRED
        ) {
            $subscription->items()->delete();
            $subscription->delete();

            return;
        }

        $subscription->type = $subscription->type
            ?? ($data['metadata']['type'] ?? $data['metadata']['name'] ?? 'default');

        if (! isset($data['items']['data'][0])) {
            return;
        }

        $firstItem = $data['items']['data'][0];
        $isSinglePrice = count($data['items']['data']) === 1;

        $subscription->stripe_price = $isSinglePrice ? $firstItem['price']['id'] : null;
        $subscription->quantity = $isSinglePrice && isset($firstItem['quantity']) ? $firstItem['quantity'] : null;

        if (isset($data['trial_end'])) {
            $trialEnd = Carbon::createFromTimestamp($data['trial_end']);
            if (! $subscription->trial_ends_at || $subscription->trial_ends_at->ne($trialEnd)) {
                $subscription->trial_ends_at = $trialEnd;
            }
        }

        if ($data['cancel_at_period_end'] ?? false) {
            $subscription->ends_at = $subscription->onTrial()
                ? $subscription->trial_ends_at
                : $subscription->currentPeriodEnd();
        } elseif (isset($data['cancel_at']) || isset($data['canceled_at'])) {
            $subscription->ends_at = Carbon::createFromTimestamp($data['cancel_at'] ?? $data['canceled_at']);
        } else {
            $subscription->ends_at = null;
        }

        if (isset($data['status'])) {
            $subscription->stripe_status = $data['status'];
        }

        $subscription->save();

        if (isset($data['items']['data'])) {
            $itemIds = [];
            foreach ($data['items']['data'] as $item) {
                $itemIds[] = $item['id'];
                $subscription->items()->updateOrCreate(
                    ['stripe_id' => $item['id']],
                    [
                        'stripe_product' => $item['price']['product'],
                        'stripe_price' => $item['price']['id'],
                        'quantity' => $item['quantity'] ?? null,
                    ]
                );
            }
            $subscription->items()->whereNotIn('stripe_id', $itemIds)->delete();
        }

        if (! is_null($user->trial_ends_at)) {
            $user->trial_ends_at = null;
            $user->save();
        }
    }
}
