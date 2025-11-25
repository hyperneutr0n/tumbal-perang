<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'data_type',
        'description',
        'is_editable',
    ];

    protected $casts = [
        'data_type' => 'string',
        'is_editable' => 'boolean',
    ];

    /**
     * Get the value cast to the appropriate type.
     */
    public function getTypedValueAttribute()
    {
        return match($this->data_type) {
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Set the value with appropriate type conversion.
     */
    public function setTypedValue($value): void
    {
        $this->value = match($this->data_type) {
            'json' => json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->typed_value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value): void
    {
        $setting = static::firstOrCreate(['key' => $key]);
        $setting->setTypedValue($value);
        $setting->save();
    }
}
