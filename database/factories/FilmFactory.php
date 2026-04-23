<?php

namespace Database\Factories;

use App\Models\Film;
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
        return [
            'title' => Str::title($this->faker->words(random_int(2, 4), true)),
            'release_year' => $this->faker->numberBetween(1985, (int) date('Y')),
            'synopsis' => $this->faker->optional(0.9)->text(300),
        ];
    }
}
