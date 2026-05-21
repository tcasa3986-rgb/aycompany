<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = ['clave', 'valor', 'tipo', 'descripcion'];

    public static function get(string $clave, $default = null)
    {
        $config = self::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    public static function set(string $clave, $valor): void
    {
        self::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }
}
