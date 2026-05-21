<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use Carbon\Carbon;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['nombre' => 'Juan',     'apellido' => 'García López',     'telefono' => '987654321', 'email' => 'juan@email.com',     'direccion' => 'Av. Arequipa 1234',    'distrito' => 'Miraflores',  'tipo' => 'vip'],
            ['nombre' => 'María',    'apellido' => 'Torres Silva',     'telefono' => '976543210', 'email' => 'maria@email.com',    'direccion' => 'Jr. Lima 567',         'distrito' => 'San Isidro',  'tipo' => 'frecuente'],
            ['nombre' => 'Carlos',   'apellido' => 'Ruiz Méndez',      'telefono' => '965432109', 'email' => 'carlos@email.com',   'direccion' => 'Av. Brasil 890',       'distrito' => 'Pueblo Libre','tipo' => 'regular'],
            ['nombre' => 'Ana',      'apellido' => 'Flores Castro',    'telefono' => '954321098', 'email' => 'ana@email.com',      'direccion' => 'Calle Los Pinos 12',   'distrito' => 'Surco',       'tipo' => 'frecuente'],
            ['nombre' => 'Luis',     'apellido' => 'Mamani Quispe',    'telefono' => '943210987', 'email' => null,                 'direccion' => 'Av. Colonial 456',     'distrito' => 'Cercado',     'tipo' => 'regular'],
            ['nombre' => 'Rosa',     'apellido' => 'Vargas Huanca',    'telefono' => '932109876', 'email' => 'rosa@email.com',     'direccion' => 'Jr. Tacna 789',        'distrito' => 'Breña',       'tipo' => 'regular'],
            ['nombre' => 'Pedro',    'apellido' => 'Díaz Ccori',       'telefono' => '921098765', 'email' => null,                 'direccion' => 'Av. Universitaria 88', 'distrito' => 'Los Olivos',  'tipo' => 'vip'],
            ['nombre' => 'Elena',    'apellido' => 'Chávez Ramos',     'telefono' => '910987654', 'email' => 'elena@email.com',    'direccion' => 'Calle Real 234',       'distrito' => 'San Borja',   'tipo' => 'frecuente'],
            ['nombre' => 'Miguel',   'apellido' => 'Salazar Rojas',    'telefono' => '909876543', 'email' => 'miguel@email.com',   'direccion' => 'Av. La Marina 1500',   'distrito' => 'San Miguel',  'tipo' => 'regular'],
            ['nombre' => 'Patricia', 'apellido' => 'Morales Aguilar',  'telefono' => '898765432', 'email' => 'patricia@email.com', 'direccion' => 'Calle Schell 250',     'distrito' => 'Miraflores',  'tipo' => 'vip'],
            ['nombre' => 'Roberto',  'apellido' => 'Quispe Yauri',     'telefono' => '887654321', 'email' => 'roberto@email.com',  'direccion' => 'Jr. Cusco 410',        'distrito' => 'La Victoria', 'tipo' => 'regular'],
            ['nombre' => 'Sofía',    'apellido' => 'Castillo Bravo',   'telefono' => '876543210', 'email' => 'sofia@email.com',    'direccion' => 'Av. Aviación 3300',    'distrito' => 'San Borja',   'tipo' => 'frecuente'],
        ];

        foreach ($clientes as $i => $c) {
            $created = Carbon::now()->subDays(rand(0, 60))->subHours(rand(0,23));
            Cliente::firstOrCreate(
                ['telefono' => $c['telefono']],
                array_merge($c, [
                    'ciudad'     => 'Lima',
                    'activo'     => true,
                    'created_at' => $created,
                    'updated_at' => $created,
                ])
            );
        }
    }
}
