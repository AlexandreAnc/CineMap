<?php

namespace Database\Factories;

use App\Models\Film;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        $cities = ['Paris', 'Lyon', 'Marseille', 'Rouen', 'Nantes'];
        $countries = ['France', 'Belgique', 'Suisse'];
        $city = $cities[array_rand($cities)];

        return [
            'film_id' => Film::factory(),
            'user_id' => User::factory(),
            'name' => 'Lieu '.Str::upper(Str::random(4)),
            'city' => $city,
            'country' => $countries[array_rand($countries)],
            'description' => 'Description simple du lieu pour les tests.',
            'upvotes_count' => 0,
        ];
    }
}
