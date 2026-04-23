<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Film;
use Illuminate\Http\JsonResponse;

/**
 * API en lecture seule pour le serveur MCP (et tests manuels avec curl).
 * Même idée de données que le reste de l’appli, sans abonnement ni JWT.
 */
class McpReadController extends Controller
{
    /**
     * Outil list_films : tous les films.
     */
    public function films(): JsonResponse
    {
        $rows = Film::query()
            ->orderBy('title')
            ->get(['id', 'title', 'release_year', 'synopsis']);

        return response()->json(['films' => $rows]);
    }

    /**
     * Outil get_locations_for_film : emplacements d’un film.
     */
    public function filmLocations(Film $film): JsonResponse
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
