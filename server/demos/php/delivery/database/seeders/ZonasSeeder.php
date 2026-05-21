<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zona;

class ZonasSeeder extends Seeder
{
    public function run(): void
    {
        $zonas = [
            ['nombre' => 'Centro Histórico',  'distrito' => 'Cercado de Lima',  'costo_delivery' => 6.00,  'tiempo_estimado_min' => 30, 'monto_minimo_pedido' => 15.00],
            ['nombre' => 'Miraflores',        'distrito' => 'Miraflores',       'costo_delivery' => 8.00,  'tiempo_estimado_min' => 35, 'monto_minimo_pedido' => 20.00],
            ['nombre' => 'San Isidro',        'distrito' => 'San Isidro',       'costo_delivery' => 8.00,  'tiempo_estimado_min' => 35, 'monto_minimo_pedido' => 20.00],
            ['nombre' => 'Surco / La Molina', 'distrito' => 'Surco',            'costo_delivery' => 10.00, 'tiempo_estimado_min' => 45, 'monto_minimo_pedido' => 25.00],
            ['nombre' => 'San Borja',         'distrito' => 'San Borja',        'costo_delivery' => 9.00,  'tiempo_estimado_min' => 40, 'monto_minimo_pedido' => 20.00],
            ['nombre' => 'Barranco',          'distrito' => 'Barranco',         'costo_delivery' => 9.00,  'tiempo_estimado_min' => 40, 'monto_minimo_pedido' => 20.00],
            ['nombre' => 'Pueblo Libre',      'distrito' => 'Pueblo Libre',     'costo_delivery' => 7.00,  'tiempo_estimado_min' => 35, 'monto_minimo_pedido' => 15.00],
            ['nombre' => 'San Miguel',        'distrito' => 'San Miguel',       'costo_delivery' => 7.00,  'tiempo_estimado_min' => 35, 'monto_minimo_pedido' => 15.00],
            ['nombre' => 'Los Olivos / SMP',  'distrito' => 'Los Olivos',       'costo_delivery' => 12.00, 'tiempo_estimado_min' => 55, 'monto_minimo_pedido' => 25.00],
            ['nombre' => 'Ate / Santa Anita', 'distrito' => 'Ate',              'costo_delivery' => 13.00, 'tiempo_estimado_min' => 60, 'monto_minimo_pedido' => 30.00],
        ];

        foreach ($zonas as $z) {
            Zona::firstOrCreate(['nombre' => $z['nombre']], array_merge($z, ['activo' => true]));
        }
    }
}
