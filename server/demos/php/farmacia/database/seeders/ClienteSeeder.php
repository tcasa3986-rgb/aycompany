<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['documento' => '00000000', 'nombres' => 'Cliente', 'apellidos' => 'Genérico', 'telefono' => '000000000'],
            ['documento' => '12345678', 'nombres' => 'María',   'apellidos' => 'García López', 'telefono' => '987654321', 'alergias' => 'Penicilina'],
            ['documento' => '23456789', 'nombres' => 'Carlos',  'apellidos' => 'Mendoza Ruiz', 'telefono' => '976543210', 'enfermedades_cronicas' => 'Hipertensión'],
            ['documento' => '34567890', 'nombres' => 'Lucía',   'apellidos' => 'Pérez Silva',  'telefono' => '965432109'],
            ['documento' => '45678901', 'nombres' => 'Jorge',   'apellidos' => 'Vargas Soto',  'telefono' => '954321098', 'enfermedades_cronicas' => 'Diabetes tipo 2'],
        ];

        foreach ($clientes as $c) {
            Cliente::updateOrCreate(['documento' => $c['documento']], $c);
        }
    }
}
