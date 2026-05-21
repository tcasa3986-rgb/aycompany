<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // Empresa
            ['clave' => 'empresa_nombre',    'valor' => 'CRM Delivery',           'grupo' => 'empresa',   'tipo' => 'text'],
            ['clave' => 'empresa_ruc',       'valor' => '20123456789',             'grupo' => 'empresa',   'tipo' => 'text'],
            ['clave' => 'empresa_telefono',  'valor' => '01-234-5678',             'grupo' => 'empresa',   'tipo' => 'text'],
            ['clave' => 'empresa_email',     'valor' => 'info@crmdelivery.com',    'grupo' => 'empresa',   'tipo' => 'text'],
            ['clave' => 'empresa_direccion', 'valor' => 'Av. Principal 123, Lima', 'grupo' => 'empresa',   'tipo' => 'text'],
            ['clave' => 'empresa_web',       'valor' => 'www.crmdelivery.com',     'grupo' => 'empresa',   'tipo' => 'text'],
            // Delivery
            ['clave' => 'delivery_costo_base',   'valor' => '5.00',  'grupo' => 'delivery', 'tipo' => 'number'],
            ['clave' => 'delivery_tiempo_est',   'valor' => '45',    'grupo' => 'delivery', 'tipo' => 'number'],
            ['clave' => 'delivery_radio_km',     'valor' => '10',    'grupo' => 'delivery', 'tipo' => 'number'],
            // Sistema
            ['clave' => 'moneda_simbolo',    'valor' => 'S/',           'grupo' => 'sistema', 'tipo' => 'text'],
            ['clave' => 'moneda_codigo',     'valor' => 'PEN',          'grupo' => 'sistema', 'tipo' => 'text'],
            ['clave' => 'timezone',          'valor' => 'America/Lima', 'grupo' => 'sistema', 'tipo' => 'text'],
            ['clave' => 'pedidos_por_pagina','valor' => '20',           'grupo' => 'sistema', 'tipo' => 'number'],
            // Notificaciones
            ['clave' => 'notif_email',       'valor' => '1', 'grupo' => 'notificaciones', 'tipo' => 'number', 'descripcion' => 'Enviar email al cliente en cambios de estado (1=sí, 0=no)'],
            ['clave' => 'notif_whatsapp',    'valor' => '1', 'grupo' => 'notificaciones', 'tipo' => 'number', 'descripcion' => 'Mostrar botón Notificar por WhatsApp en pedidos'],
        ];

        foreach ($configs as $config) {
            Configuracion::firstOrCreate(['clave' => $config['clave']], $config);
        }
    }
}
