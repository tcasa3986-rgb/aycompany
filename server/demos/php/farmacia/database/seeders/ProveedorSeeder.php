<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            ['ruc' => '20100070970', 'razon_social' => 'Laboratorios Bayer S.A.',     'contacto' => 'Juan Pérez',  'telefono' => '014567890', 'email' => 'ventas@bayer.com'],
            ['ruc' => '20101111111', 'razon_social' => 'Pfizer Perú S.A.',            'contacto' => 'María Soto',  'telefono' => '014444444', 'email' => 'pedidos@pfizer.com'],
            ['ruc' => '20102222222', 'razon_social' => 'Genfar Perú S.A.',            'contacto' => 'Luis Quispe', 'telefono' => '015555555', 'email' => 'contacto@genfar.pe'],
            ['ruc' => '20103333333', 'razon_social' => 'Distribuidora Drokasa S.A.',  'contacto' => 'Ana Vega',    'telefono' => '016666666', 'email' => 'comercial@drokasa.pe'],
        ];

        foreach ($proveedores as $p) {
            Proveedor::firstOrCreate(['ruc' => $p['ruc']], $p);
        }
    }
}
