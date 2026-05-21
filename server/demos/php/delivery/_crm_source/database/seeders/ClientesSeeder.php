<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['nombre' => 'Juan', 'apellido' => 'García López',   'telefono' => '987654321', 'email' => 'juan@email.com',   'direccion' => 'Av. Arequipa 1234', 'distrito' => 'Miraflores', 'tipo' => 'vip'],
            ['nombre' => 'María', 'apellido' => 'Torres Silva',  'telefono' => '976543210', 'email' => 'maria@email.com',  'direccion' => 'Jr. Lima 567',      'distrito' => 'San Isidro',  'tipo' => 'frecuente'],
            ['nombre' => 'Carlos', 'apellido' => 'Ruiz Mendez',  'telefono' => '965432109', 'email' => 'carlos@email.com', 'direccion' => 'Av. Brasil 890',    'distrito' => 'Pueblo Libre','tipo' => 'regular'],
            ['nombre' => 'Ana', 'apellido' => 'Flores Castro',   'telefono' => '954321098', 'email' => 'ana@email.com',    'direccion' => 'Calle Los Pinos 12','distrito' => 'Surco',       'tipo' => 'frecuente'],
            ['nombre' => 'Luis', 'apellido' => 'Mamani Quispe',  'telefono' => '943210987', 'email' => null,               'direccion' => 'Av. Colonial 456',  'distrito' => 'Cercado',     'tipo' => 'regular'],
            ['nombre' => 'Rosa', 'apellido' => 'Vargas Huanca',  'telefono' => '932109876', 'email' => 'rosa@email.com',   'direccion' => 'Jr. Tacna 789',     'distrito' => 'Breña',       'tipo' => 'regular'],
            ['nombre' => 'Pedro', 'apellido' => 'Díaz Ccori',    'telefono' => '921098765', 'email' => null,               'direccion' => 'Av. Universitaria', 'distrito' => 'Los Olivos',  'tipo' => 'vip'],
            ['nombre' => 'Elena', 'apellido' => 'Chávez Ramos',  'telefono' => '910987654', 'email' => 'elena@email.com',  'direccion' => 'Calle Real 234',    'distrito' => 'San Borja',   'tipo' => 'frecuente'],
        ];

        foreach ($clientes as $c) {
            Cliente::firstOrCreate(
                ['telefono' => $c['telefono']],
                array_merge($c, ['ciudad' => 'Lima', 'activo' => true])
            );
        }
    }
}
