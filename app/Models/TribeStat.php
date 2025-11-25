<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TribeStat extends Model
{
    protected $fillable = [
        'tribe_id',
        'stat_type_id',
        'value',
    ];

    protected $casts = [
        'value' => 'integer',
        'tribe_id' => 'integer',
        'stat_type_id' => 'integer',
    ];

    /**
     * Get the tribe that owns this stat.
     */
    public function tribe(): BelongsTo
    {
        return $this->belongsTo(Tribe::class);
    }

    /**
     * Get the stat type for this tribe stat.
     */
    public function statType(): BelongsTo
    {
        return $this->belongsTo(StatType::class);
    }
}
