<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Repartidor;
use App\Models\User;

class RepartidoresSeeder extends Seeder
{
    public function run(): void
    {
        $userRepartidor = User::where('email', 'repartidor@crm.com')->first();

        $repartidores = [
            ['nombre' => 'Carlos',   'apellido' => 'Quispe Mamani', 'dni' => '12345678', 'telefono' => '999111001', 'tipo_vehiculo' => 'moto',      'zona_asignada' => 'Miraflores, San Isidro', 'estado' => 'disponible', 'user_id' => $userRepartidor?->id],
            ['nombre' => 'Jorge',    'apellido' => 'Flores Inca',   'dni' => '23456789', 'telefono' => '999111002', 'tipo_vehiculo' => 'moto',      'zona_asignada' => 'Surco, La Molina',      'estado' => 'disponible', 'user_id' => null],
            ['nombre' => 'Miguel',   'apellido' => 'Ramos Torres',  'dni' => '34567890', 'telefono' => '999111003', 'tipo_vehiculo' => 'bicicleta', 'zona_asignada' => 'Barranco, Chorrillos',  'estado' => 'disponible', 'user_id' => null],
            ['nombre' => 'Luis',     'apellido' => 'Paucar Huanca', 'dni' => '45678901', 'telefono' => '999111004', 'tipo_vehiculo' => 'auto',      'zona_asignada' => 'San Borja, Surquillo',  'estado' => 'descanso',   'user_id' => null],
            ['nombre' => 'Andrés',   'apellido' => 'Cárdenas Ruiz', 'dni' => '56789012', 'telefono' => '999111005', 'tipo_vehiculo' => 'moto',      'zona_asignada' => 'Los Olivos, SMP',       'estado' => 'inactivo',   'user_id' => null],
        ];

        foreach ($repartidores as $r) {
            Repartidor::firstOrCreate(
                ['dni' => $r['dni']],
                array_merge($r, ['activo' => true, 'calificacion' => 4.80])
            );
        }
    }
}
