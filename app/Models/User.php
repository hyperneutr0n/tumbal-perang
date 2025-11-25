<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'tribe_id',
        'gold',
        'troops',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tribe_id' => 'integer',
            'gold' => 'integer',
            'troops' => 'integer',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the tribe that the user belongs to.
     */
    public function tribe(): BelongsTo
    {
        return $this->belongsTo(Tribe::class);
    }

    /**
     * Get the roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the user's buildings.
     */
    public function userBuildings(): HasMany
    {
        return $this->hasMany(UserBuilding::class);
    }

    /**
     * Get battles where this user was the attacker.
     */
    public function attackerBattles(): HasMany
    {
        return $this->hasMany(Battle::class, 'attacker_id');
    }

    /**
     * Get battles where this user was the defender.
     */
    public function defenderBattles(): HasMany
    {
        return $this->hasMany(Battle::class, 'defender_id');
    }

    /**
     * Get all battles for this user (both as attacker and defender).
     */
    public function allBattles()
    {
        return Battle::where('attacker_id', $this->id)
            ->orWhere('defender_id', $this->id);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
