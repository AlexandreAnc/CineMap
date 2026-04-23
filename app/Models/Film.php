<?php

namespace App\Models;

use Database\Factories\FilmFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Film extends Model
{

    use HasFactory;

    protected $fillable = [
        'title',
        'release_year',
        'synopsis',
    ];

    protected function casts(): array
    {
        return [
            'release_year' => 'integer',
        ];
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}
