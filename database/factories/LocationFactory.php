<?php

namespace Database\Factories;

use App\Models\Film;
use App\Models\Location;
use App\Models\User;
use Faker\Factory as FakerFactory;
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
        $faker = FakerFactory::create();

        return [
            'film_id' => Film::factory(),
            'user_id' => User::factory(),
            'name' => Str::title($faker->words(2, true)).' — '.$faker->city(),
            'city' => $faker->city(),
            'country' => $faker->country(),
            'description' => $faker->optional(0.85)->text(200),
            'upvotes_count' => 0,
        ];
    }
}
