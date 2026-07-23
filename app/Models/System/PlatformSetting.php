<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $table = 'platform_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public static function setValue(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
