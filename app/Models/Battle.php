<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Battle extends Model
{
    protected $fillable = [
        'attacker_id',
        'defender_id',
        'attacker_troops',
        'defender_troops',
        'attacker_power',
        'defender_power',
        'result',
        'gold_stolen',
        'attacker_troops_lost',
        'defender_troops_lost',
    ];

    protected $casts = [
        'attacker_id' => 'integer',
        'defender_id' => 'integer',
        'attacker_troops' => 'integer',
        'defender_troops' => 'integer',
        'attacker_power' => 'integer',
        'defender_power' => 'integer',
        'result' => 'string',
        'gold_stolen' => 'integer',
        'attacker_troops_lost' => 'integer',
        'defender_troops_lost' => 'integer',
    ];

    /**
     * Get the attacker user.
     */
    public function attacker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attacker_id');
    }

    /**
     * Get the defender user.
     */
    public function defender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'defender_id');
    }
}
