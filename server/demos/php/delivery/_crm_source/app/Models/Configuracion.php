<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracion extends Model
{
    protected $fillable = ['clave', 'valor', 'tipo', 'grupo', 'descripcion'];

    protected $table = 'configuraciones';

    public static function obtener(string $clave, mixed $default = null): mixed
    {
        return Cache::remember("config_{$clave}", 3600, function () use ($clave, $default) {
            $config = static::where('clave', $clave)->first();
            return $config ? $config->valor : $default;
        });
    }

    public static function establecer(string $clave, mixed $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        Cache::forget("config_{$clave}");
    }
}
