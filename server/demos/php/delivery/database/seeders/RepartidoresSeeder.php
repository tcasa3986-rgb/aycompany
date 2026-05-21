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
            ['nombre' => 'Carlos',   'apellido' => 'Quispe Mamani',  'dni' => '12345678', 'telefono' => '999111001', 'tipo_vehiculo' => 'moto',      'placa' => 'ABC-101', 'zona_asignada' => 'Miraflores, San Isidro', 'estado' => 'disponible', 'user_id' => $userRepartidor?->id, 'calificacion' => 4.85],
            ['nombre' => 'Jorge',    'apellido' => 'Flores Inca',    'dni' => '23456789', 'telefono' => '999111002', 'tipo_vehiculo' => 'moto',      'placa' => 'ABC-102', 'zona_asignada' => 'Surco, La Molina',       'estado' => 'disponible', 'user_id' => null, 'calificacion' => 4.70],
            ['nombre' => 'Miguel',   'apellido' => 'Ramos Torres',   'dni' => '34567890', 'telefono' => '999111003', 'tipo_vehiculo' => 'bicicleta', 'placa' => null,      'zona_asignada' => 'Barranco, Chorrillos',   'estado' => 'disponible', 'user_id' => null, 'calificacion' => 4.50],
            ['nombre' => 'Luis',     'apellido' => 'Paucar Huanca',  'dni' => '45678901', 'telefono' => '999111004', 'tipo_vehiculo' => 'auto',      'placa' => 'XYZ-200', 'zona_asignada' => 'San Borja, Surquillo',   'estado' => 'descanso',   'user_id' => null, 'calificacion' => 4.90],
            ['nombre' => 'Andrés',   'apellido' => 'Cárdenas Ruiz',  'dni' => '56789012', 'telefono' => '999111005', 'tipo_vehiculo' => 'moto',      'placa' => 'ABC-103', 'zona_asignada' => 'Los Olivos, SMP',        'estado' => 'inactivo',   'user_id' => null, 'calificacion' => 4.20],
            ['nombre' => 'Daniel',   'apellido' => 'Huamán Pérez',   'dni' => '67890123', 'telefono' => '999111006', 'tipo_vehiculo' => 'moto',      'placa' => 'ABC-104', 'zona_asignada' => 'San Miguel, Magdalena',  'estado' => 'disponible', 'user_id' => null, 'calificacion' => 4.65],
            ['nombre' => 'Roberto',  'apellido' => 'Vega Mendoza',   'dni' => '78901234', 'telefono' => '999111007', 'tipo_vehiculo' => 'moto',      'placa' => 'ABC-105', 'zona_asignada' => 'Cercado, Breña',         'estado' => 'disponible', 'user_id' => null, 'calificacion' => 4.40],
            ['nombre' => 'Pablo',    'apellido' => 'Núñez Salinas',  'dni' => '89012345', 'telefono' => '999111008', 'tipo_vehiculo' => 'bicicleta', 'placa' => null,      'zona_asignada' => 'Pueblo Libre, Jesús María','estado' => 'ocupado',    'user_id' => null, 'calificacion' => 4.55],
            ['nombre' => 'José',     'apellido' => 'Aliaga Vilca',   'dni' => '90123456', 'telefono' => '999111009', 'tipo_vehiculo' => 'moto',      'placa' => 'ABC-106', 'zona_asignada' => 'Independencia, Comas',   'estado' => 'disponible', 'user_id' => null, 'calificacion' => 4.75],
            ['nombre' => 'Walter',   'apellido' => 'Tapia Choque',   'dni' => '01234567', 'telefono' => '999111010', 'tipo_vehiculo' => 'moto',      'placa' => 'ABC-107', 'zona_asignada' => 'Ate, Santa Anita',       'estado' => 'disponible', 'user_id' => null, 'calificacion' => 4.30],
        ];

        foreach ($repartidores as $r) {
            Repartidor::firstOrCreate(
                ['dni' => $r['dni']],
                array_merge($r, ['activo' => true])
            );
        }
    }
}
