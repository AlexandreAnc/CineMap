<?php

use App\Jobs\SyncLocationUpvotesCount;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

test('user can add vote and dispatch sync job', function () {
    Bus::fake();

    $user = User::factory()->create();
    $location = Location::factory()->create();

    $this->actingAs($user)
        ->postJson(route('locations.vote', $location))
        ->assertOk()
        ->assertJsonPath('has_voted', true)
        ->assertJsonPath('upvotes_count', 1);

    $this->assertDatabaseHas('location_votes', [
        'user_id' => $user->id,
        'location_id' => $location->id,
    ]);

    Bus::assertDispatched(SyncLocationUpvotesCount::class, function (SyncLocationUpvotesCount $job) use ($location) {
        return $job->locationId === $location->id;
    });
});

test('user can remove vote and dispatch sync job again', function () {
    Bus::fake();

    $user = User::factory()->create();
    $location = Location::factory()->create();

    $this->actingAs($user)->postJson(route('locations.vote', $location));

    $this->actingAs($user)
        ->postJson(route('locations.vote', $location))
        ->assertOk()
        ->assertJsonPath('has_voted', false)
        ->assertJsonPath('upvotes_count', 0);

    $this->assertDatabaseMissing('location_votes', [
        'user_id' => $user->id,
        'location_id' => $location->id,
    ]);

    Bus::assertDispatchedTimes(SyncLocationUpvotesCount::class, 2);
});
