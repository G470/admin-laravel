<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        return $setting->save();
    }

    public static function getGroup($group)
    {
        return static::where('group', $group)->get()->pluck('value', 'key')->toArray();
    }

    public static function setGroup($group, array $values)
    {
        foreach ($values as $key => $value) {
            static::set($key, $value);
        }
    }
}