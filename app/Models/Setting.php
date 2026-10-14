<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Anahtara göre ayar değerini getirir veya varsayılanı döner.
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return ($setting && $setting->value !== null && $setting->value !== '') ? $setting->value : $default;
    }

    /**
     * Ayarı günceller veya oluşturur.
     */
    public static function set(string $key, $value): static
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
