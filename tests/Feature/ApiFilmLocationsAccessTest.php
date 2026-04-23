<?php

use App\Models\Film;
use App\Models\Location;
use App\Models\User;

test('api film locations requires jwt token', function () {
    $film = Film::factory()->create();

    $this->getJson("/api/films/{$film->id}/locations")
        ->assertStatus(401);
});

test('api film locations rejects jwt user without premium', function () {
    $user = User::factory()->create();
    $film = Film::factory()->create();

    $tokenResponse = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $token = $tokenResponse->json('access_token');

    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/films/{$film->id}/locations")
        ->assertForbidden()
        ->assertJsonPath('message', 'Un abonnement actif est requis.');
});

test('api film locations returns data for jwt premium user', function () {
    $user = User::factory()->create([
        'stripe_id' => 'cus_test_api_premium_user',
    ]);
    $film = Film::factory()->create([
        'title' => 'Inception',
    ]);

    Location::factory()->create([
        'film_id' => $film->id,
        'user_id' => $user->id,
        'name' => 'Pont de Bir-Hakeim',
        'upvotes_count' => 3,
    ]);

    $user->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test_api_premium_user',
        'stripe_status' => 'active',
        'stripe_price' => 'price_test_premium',
        'quantity' => 1,
    ]);

    $tokenResponse = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $token = $tokenResponse->json('access_token');

    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/films/{$film->id}/locations")
        ->assertOk()
        ->assertJsonPath('film.title', 'Inception')
        ->assertJsonPath('locations.0.name', 'Pont de Bir-Hakeim')
        ->assertJsonPath('locations.0.upvotes_count', 3);
});
