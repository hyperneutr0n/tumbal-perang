<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBuilding extends Model
{
    protected $fillable = [
        'user_id',
        'building_id',
        'built_at',
        'level',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'building_id' => 'integer',
        'built_at' => 'datetime',
        'level' => 'integer',
    ];

    /**
     * Get the user that owns this building.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the building.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
