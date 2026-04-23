<?php

use App\Models\User;

test('guest cannot access private page', function () {
    $this->get('/prive')
        ->assertRedirect('/login');
});

test('authenticated user without premium is redirected to subscribe page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/prive')
        ->assertRedirect(route('subscribe', absolute: false));
});

test('authenticated premium user can access private page', function () {
    $user = User::factory()->create([
        'stripe_id' => 'cus_test_premium_user',
    ]);

    $user->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test_premium_user',
        'stripe_status' => 'active',
        'stripe_price' => 'price_test_premium',
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->get('/prive')
        ->assertOk()
        ->assertSee('abonnement premium actif');
});
