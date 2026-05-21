<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracion extends Model
{
    protected $table    = 'configuracion';
    protected $fillable = ['clave', 'valor', 'descripcion', 'grupo'];

    /**
     * Obtener un valor de configuración por clave.
     */
    public static function get(string $clave, mixed $default = null): mixed
    {
        return Cache::remember("config_{$clave}", 3600, function () use ($clave, $default) {
            $config = static::where('clave', $clave)->first();
            return $config ? $config->valor : $default;
        });
    }

    /**
     * Guardar o actualizar un valor de configuración.
     */
    public static function set(string $clave, mixed $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        Cache::forget("config_{$clave}");
    }

    /**
     * Obtener todas las configs agrupadas.
     */
    public static function allAgrupadas(): array
    {
        return static::all()->groupBy('grupo')->toArray();
    }

    /**
     * Helpers de acceso rápido.
     */
    public static function nombreColegio(): string   { return static::get('colegio_nombre', 'Colegio CRM'); }
    public static function anioEscolar(): int        { return (int) static::get('anio_escolar', date('Y')); }
    public static function notaMinima(): float       { return (float) static::get('nota_minima', 11); }
    public static function moneda(): string          { return static::get('moneda', 'S/.'); }
}
