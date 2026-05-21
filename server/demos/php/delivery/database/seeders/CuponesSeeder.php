<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cupon;
use Carbon\Carbon;

class CuponesSeeder extends Seeder
{
    public function run(): void
    {
        $cupones = [
            ['codigo' => 'BIENVENIDA10',   'descripcion' => '10% de descuento para tu primer pedido', 'tipo' => 'porcentaje', 'valor' => 10, 'monto_minimo' => 20,  'descuento_maximo' => 15, 'usos_maximos' => null, 'solo_primer_pedido' => true,  'valido_hasta' => Carbon::now()->addMonths(6)],
            ['codigo' => 'VERANO20',       'descripcion' => '20% verano - todos los pedidos',         'tipo' => 'porcentaje', 'valor' => 20, 'monto_minimo' => 30,  'descuento_maximo' => 20, 'usos_maximos' => 200,  'solo_primer_pedido' => false, 'valido_hasta' => Carbon::now()->addMonths(2)],
            ['codigo' => 'DELIVERYGRATIS', 'descripcion' => 'Envío gratis en pedidos grandes',       'tipo' => 'monto',      'valor' => 10, 'monto_minimo' => 50,  'descuento_maximo' => null,'usos_maximos' => 100,  'solo_primer_pedido' => false, 'valido_hasta' => Carbon::now()->addMonths(1)],
            ['codigo' => 'CUMPLE15',       'descripcion' => 'Descuento de cumpleaños',                'tipo' => 'porcentaje', 'valor' => 15, 'monto_minimo' => 25,  'descuento_maximo' => 25, 'usos_maximos' => null, 'solo_primer_pedido' => false, 'valido_hasta' => Carbon::now()->addMonths(12)],
            ['codigo' => 'FIN5',           'descripcion' => 'S/5 off fin de semana',                  'tipo' => 'monto',      'valor' => 5,  'monto_minimo' => 30,  'descuento_maximo' => null,'usos_maximos' => 500,  'solo_primer_pedido' => false, 'valido_hasta' => Carbon::now()->addMonths(3)],
            ['codigo' => 'COMBO50',        'descripcion' => 'S/10 off combos sobre S/50',             'tipo' => 'monto',      'valor' => 10, 'monto_minimo' => 50,  'descuento_maximo' => null,'usos_maximos' => 50,   'solo_primer_pedido' => false, 'valido_hasta' => Carbon::now()->addMonths(1)],
            ['codigo' => 'PRIMERA5',       'descripcion' => 'S/5 a tu primer pedido',                 'tipo' => 'monto',      'valor' => 5,  'monto_minimo' => 15,  'descuento_maximo' => null,'usos_maximos' => null, 'solo_primer_pedido' => true,  'valido_hasta' => Carbon::now()->addMonths(6)],
            ['codigo' => 'PROMO25',        'descripcion' => '25% off pedidos premium',                'tipo' => 'porcentaje', 'valor' => 25, 'monto_minimo' => 80,  'descuento_maximo' => 30, 'usos_maximos' => 30,   'solo_primer_pedido' => false, 'valido_hasta' => Carbon::now()->addMonths(2)],
            ['codigo' => 'NAVIDAD30',      'descripcion' => '30% campaña navideña',                   'tipo' => 'porcentaje', 'valor' => 30, 'monto_minimo' => 100, 'descuento_maximo' => 50, 'usos_maximos' => 50,   'solo_primer_pedido' => false, 'valido_hasta' => Carbon::now()->addMonths(11)],
            ['codigo' => 'EXPIRADO',       'descripcion' => 'Cupón expirado (test)',                  'tipo' => 'porcentaje', 'valor' => 50, 'monto_minimo' => 0,   'descuento_maximo' => null,'usos_maximos' => null, 'solo_primer_pedido' => false, 'valido_hasta' => Carbon::now()->subDays(10), 'activo' => false],
        ];

        foreach ($cupones as $c) {
            Cupon::firstOrCreate(
                ['codigo' => $c['codigo']],
                array_merge([
                    'valido_desde' => Carbon::now()->subDays(5),
                    'usos_actuales'=> rand(0, 8),
                    'activo'       => true,
                ], $c)
            );
        }
    }
}
