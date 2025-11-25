<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'attack_type',
    ];

    protected $casts = [
        'category' => 'string',
        'attack_type' => 'string',
    ];

    /**
     * Get the tribe stats for this stat type.
     */
    public function tribeStats(): HasMany
    {
        return $this->hasMany(TribeStat::class);
    }
}
