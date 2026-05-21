<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $fillable = [
        'clave',
        'valor',
    ];

    public static function get(string $key, $default = null)
    {
        $config = self::where('clave', $key)->first();
        return $config ? $config->valor : $default;
    }

    public static function set(string $key, string $value): void
    {
        self::updateOrCreate(
            ['clave' => $key],
            ['valor' => $value]
        );
    }
}
