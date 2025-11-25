<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = [
        'name',
        'code',
        'price',
        'description',
        'is_unique',
        'max_quantity',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_unique' => 'boolean',
        'max_quantity' => 'integer',
    ];

    /**
     * Get the effects for this building.
     */
    public function buildingEffects(): HasMany
    {
        return $this->hasMany(BuildingEffect::class);
    }

    /**
     * Get the user buildings for this building.
     */
    public function userBuildings(): HasMany
    {
        return $this->hasMany(UserBuilding::class);
    }
}
