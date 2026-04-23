<?php

use App\Http\Controllers\FilmController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::view('/prive', 'prive')->middleware('subscribed')->name('prive');

    Route::get('films', [FilmController::class, 'index'])->name('films.index');
    Route::get('films/{film}', [FilmController::class, 'show'])->name('films.show')->whereNumber('film');

    Route::post('locations/{location}/vote', [LocationController::class, 'toggleVote'])->name('locations.vote');
    Route::resource('locations', LocationController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('subscribe', [SubscriptionController::class, 'show'])->name('subscribe');
    Route::post('subscribe/checkout', [SubscriptionController::class, 'checkout'])->name('subscribe.checkout');
    Route::get('subscribe/success', [SubscriptionController::class, 'success'])->name('subscribe.success');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('films/create', [FilmController::class, 'create'])->name('films.create');
    Route::post('films', [FilmController::class, 'store'])->name('films.store');
    Route::get('films/{film}/edit', [FilmController::class, 'edit'])->name('films.edit');
    Route::match(['put', 'patch'], 'films/{film}', [FilmController::class, 'update'])->name('films.update');
    Route::delete('films/{film}', [FilmController::class, 'destroy'])->name('films.destroy');
});

require __DIR__.'/auth.php';
