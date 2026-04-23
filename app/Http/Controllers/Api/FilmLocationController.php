<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Film;
use Illuminate\Http\JsonResponse;

class FilmLocationController extends Controller
{
    public function show(Film $film): JsonResponse
    {
        $film->load(['locations' => function ($q) {
            $q->orderBy('name');
        }]);

        return response()->json([
            'film' => [
                'id' => $film->id,
                'title' => $film->title,
                'release_year' => $film->release_year,
                'synopsis' => $film->synopsis,
            ],
            'locations' => $film->locations->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'city' => $location->city,
                    'country' => $location->country,
                    'description' => $location->description,
                    'upvotes_count' => $location->upvotes_count,
                ];
            }),
        ]);
    }
}
