<?php

use App\Http\Controllers\Api\ApiTokenController;
use App\Http\Controllers\Api\FilmLocationController;
use App\Http\Controllers\Api\McpReadController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [ApiTokenController::class, 'store']);

/** Lecture seule pour le serveur MCP (token MCP_READ_TOKEN, voir mcp/cinemap-server). */
Route::prefix('mcp')->middleware('mcp.read')->group(function () {
    Route::get('films', [McpReadController::class, 'films'])->name('api.mcp.films');
    Route::get('films/{film}/locations', [McpReadController::class, 'filmLocations'])->name('api.mcp.films.locations');
});

Route::middleware(['auth:api', 'subscribed'])->group(function () {
    Route::get('films/{film}/locations', [FilmLocationController::class, 'show'])->name('api.films.locations');
});
