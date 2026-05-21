<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Obtener un valor de configuración con valor por defecto */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /** Guardar (upsert) un valor de configuración */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** Cargar todos los settings como array asociativo */
    public static function all_keyed(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /** Valores por defecto del sistema */
    public static function defaults(): array
    {
        return [
            'company_name'         => 'Mi Empresa S.A.C.',
            'company_ruc'          => '',
            'company_address'      => '',
            'company_phone'        => '',
            'company_email'        => '',
            'company_website'      => '',
            'default_currency'     => 'PEN',
            'default_tax_rate'     => '18',
            'quotation_prefix'     => 'COT',
            'terms_and_conditions' => "1. Los precios indicados son válidos por 30 días desde la fecha de emisión.\n2. El plazo de entrega se coordinará al confirmar el pedido.\n3. Los pagos se realizarán según lo acordado entre las partes.",
            'smtp_host'            => '',
            'smtp_port'            => '587',
            'smtp_username'        => '',
            'smtp_password'        => '',
            'smtp_encryption'      => 'tls',
            'smtp_from_address'    => 'cotizaciones@miempresa.com',
            'smtp_from_name'       => 'Mi Empresa',
        ];
    }
}
