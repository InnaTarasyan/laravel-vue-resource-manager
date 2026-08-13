<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'code',
        'name',
        'provider',
        'source',
        'has_sessions',
        'has_cities',
        'has_theaters',
    ];

    protected function casts(): array
    {
        return [
            'has_sessions' => 'boolean',
            'has_cities' => 'boolean',
            'has_theaters' => 'boolean',
        ];
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function theaters(): HasMany
    {
        return $this->hasMany(Theater::class);
    }
}
