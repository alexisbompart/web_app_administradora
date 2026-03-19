<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelcomeSetting extends Model
{
    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'seccion',
        'etiqueta',
    ];

    public static function getValue(string $clave, $default = null)
    {
        $setting = static::where('clave', $clave)->first();
        return $setting ? $setting->valor : $default;
    }

    public static function getBySection(string $seccion)
    {
        return static::where('seccion', $seccion)->get()->pluck('valor', 'clave');
    }
}
