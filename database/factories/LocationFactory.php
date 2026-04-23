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
        return [
            'film_id' => Film::factory(),
            'user_id' => User::factory(),
            'name' => Str::title($this->faker->words(2, true)).' — '.$this->faker->city(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'description' => $this->faker->optional(0.85)->realText(200),
            'upvotes_count' => 0,
        ];
    }
}
