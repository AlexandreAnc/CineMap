<?php

namespace Database\Factories;

use App\Models\Film;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Film>
 */
class FilmFactory extends Factory
{
    protected $model = Film::class;

    public function definition(): array
    {
        $faker = FakerFactory::create();

        return [
            'title' => Str::title($faker->words(random_int(2, 4), true)),
            'release_year' => $faker->numberBetween(1985, (int) date('Y')),
            'synopsis' => $faker->optional(0.9)->text(300),
        ];
    }
}
