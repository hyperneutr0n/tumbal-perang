<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tribe extends Model
{
    protected $fillable = [
        'name',
        'description',
        'troops_per_minute',
    ];

    protected $casts = [
        'troops_per_minute' => 'integer',
    ];


    public function tribeStats(): HasMany
    {
        return $this->hasMany(TribeStat::class);
    }

   
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

   
    public function characterParts(): HasMany
    {
        return $this->hasMany(CharacterPart::class);
    }
}
