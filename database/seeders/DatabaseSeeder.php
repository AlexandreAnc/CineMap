<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Comptes créés par le seed : le premium (abonnement Cashier) doit rester
     * **désactivé** après chaque `migrate:fresh --seed` (démo toujours non-pro).
     */
    private const DEMO_USER_EMAILS = [
        'admin@example.com',
        'user@example.com',
    ];

    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $user = User::query()->updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Utilisateur',
                'password' => 'password',
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        $this->seedNamedFilms($admin, $user);

        Film::factory(8)->create();

        $films = Film::all();
        $users = User::all();

        foreach (range(1, 20) as $_) {
            Location::factory()->create([
                'film_id' => $films->random()->id,
                'user_id' => $users->random()->id,
            ]);
        }

        $this->stripDemoUsersPremiumState();
    }

    /**
     * Aucun abonnement pro sur les comptes de démo (tables Cashier vides, pas de stripe_id).
     */
    private function stripDemoUsersPremiumState(): void
    {
        $userIds = User::query()
            ->whereIn('email', self::DEMO_USER_EMAILS)
            ->pluck('id');

        if ($userIds->isEmpty()) {
            return;
        }

        $subscriptionIds = DB::table('subscriptions')
            ->whereIn('user_id', $userIds)
            ->pluck('id');

        if ($subscriptionIds->isNotEmpty()) {
            DB::table('subscription_items')
                ->whereIn('subscription_id', $subscriptionIds)
                ->delete();
        }

        DB::table('subscriptions')
            ->whereIn('user_id', $userIds)
            ->delete();

        User::query()
            ->whereIn('email', self::DEMO_USER_EMAILS)
            ->update([
                'google_id' => null,
                'stripe_id' => null,
                'pm_type' => null,
                'pm_last_four' => null,
                'trial_ends_at' => null,
            ]);
    }

    private function seedNamedFilms(User $admin, User $user): void
    {
        $named = [
            ['title' => 'Les Plages du désespoir', 'release_year' => 2012, 'synopsis' => 'Drame sur le littoral.'],
            ['title' => 'Lumières de gare', 'release_year' => 1998, 'synopsis' => 'Nouvelle Vague, une nuit.'],
            ['title' => 'Dernier train pour Rouen', 'release_year' => 2004, 'synopsis' => 'Poursuite en Normandie.'],
            ['title' => 'Le Ciel au-dessus de Lyon', 'release_year' => 2018, 'synopsis' => 'Mélodrame urbain.'],
        ];

        foreach ($named as $f) {
            Film::query()->updateOrCreate(
                ['title' => $f['title']],
                [
                    'release_year' => $f['release_year'],
                    'synopsis' => $f['synopsis'],
                ]
            );
        }

        $namedLocs = [
            ['name' => 'Café de la Bourse', 'city' => 'Lyon', 'country' => 'France'],
            ['name' => 'Gare centrale (hall)', 'city' => 'Lyon', 'country' => 'France'],
            ['name' => 'Berges du quai rive', 'city' => 'Rouen', 'country' => 'France'],
            ['name' => 'Ancien hangar 7', 'city' => 'Marseille', 'country' => 'France'],
        ];

        $filmsByTitle = Film::query()
            ->whereIn('title', [
                'Le Ciel au-dessus de Lyon',
                'Dernier train pour Rouen',
            ])
            ->get()
            ->keyBy('title');

        $leCiel = $filmsByTitle->get('Le Ciel au-dessus de Lyon');
        $dernier = $filmsByTitle->get('Dernier train pour Rouen');

        if ($leCiel && $dernier) {
            foreach (array_slice($namedLocs, 0, 2) as $n) {
                Location::query()->updateOrCreate(
                    [
                        'name' => $n['name'],
                        'city' => $n['city'],
                    ],
                    [
                        'film_id' => $leCiel->id,
                        'user_id' => $user->id,
                        'country' => $n['country'],
                        'description' => 'Lieu connu de la production.',
                        'upvotes_count' => 0,
                    ]
                );
            }
            foreach (array_slice($namedLocs, 2) as $n) {
                Location::query()->updateOrCreate(
                    [
                        'name' => $n['name'],
                        'city' => $n['city'],
                    ],
                    [
                        'film_id' => $dernier->id,
                        'user_id' => $admin->id,
                        'country' => $n['country'],
                        'description' => 'Scène mémorable du film.',
                        'upvotes_count' => 0,
                    ]
                );
            }
        }
    }
}
