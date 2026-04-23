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
        $titles = [
            'Le Dernier Quai',
            'Nuit sur la Ville',
            'Les Rues Oubliees',
            'Ciel de Cinema',
            'Memoire des Lieux',
        ];

        return [
            'title' => $titles[array_rand($titles)].' '.Str::upper(Str::random(3)),
            'release_year' => random_int(1985, (int) date('Y')),
            'synopsis' => 'Synopsis de demo pour un film CineMap.',
        ];
    }
}
