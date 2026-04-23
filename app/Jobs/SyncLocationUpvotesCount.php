<?php

namespace App\Jobs;

use App\Models\Location;
use App\Models\LocationVote;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncLocationUpvotesCount implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $locationId
    ) {}

    public function handle(): void
    {
        $count = LocationVote::query()
            ->where('location_id', $this->locationId)
            ->count();

        Location::query()
            ->whereKey($this->locationId)
            ->update(['upvotes_count' => $count]);
    }
}
