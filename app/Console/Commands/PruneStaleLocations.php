<?php

namespace App\Console\Commands;

use App\Models\Location;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('locations:prune-stale')]
#[Description('Supprime les emplacements de plus de 14 jours avec moins de 2 upvotes.')]
class PruneStaleLocations extends Command
{
    public function handle(): int
    {
        $query = Location::query()
            ->where('created_at', '<', now()->subDays(14))
            ->where('upvotes_count', '<', 2);

        $count = $query->delete();

        $this->info("Emplacements supprimés : {$count}.");

        return self::SUCCESS;
    }
}
