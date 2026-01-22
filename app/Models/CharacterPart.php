<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterPart extends Model
{
    protected $fillable = [
        'tribe_id',
        'part_type',
        'name',
        'image_path',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the tribe that owns this character part.
     */
    public function tribe(): BelongsTo
    {
        return $this->belongsTo(Tribe::class);
    }
}
