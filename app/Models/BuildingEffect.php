<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuildingEffect extends Model
{
    protected $fillable = [
        'building_id',
        'key',
        'value',
        'data_type',
        'description',
    ];

    protected $casts = [
        'building_id' => 'integer',
        'data_type' => 'string',
    ];

    /**
     * Get the building that owns this effect.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get the value cast to the appropriate type.
     */
    public function getTypedValueAttribute()
    {
        return match ($this->data_type) {
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'boolean' => (bool) $this->value,
            default => $this->value,
        };
    }
}
