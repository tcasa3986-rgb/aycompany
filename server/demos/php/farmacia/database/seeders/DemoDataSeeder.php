<?php

namespace Database\Seeders;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\DetalleReceta;
use App\Models\DetalleVenta;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Receta;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $proveedor = Proveedor::first();
        $cliente = Cliente::first();
        $productos = Producto::all();

        // 1. Clientes (20 registros)
        for ($i = 101; $i <= 120; $i++) {
            Cliente::firstOrCreate(['documento' => '8888' . str_pad($i, 4, '0', STR_PAD_LEFT)], [
                'nombres' => 'Cliente Nuevo ' . $i,
                'apellidos' => 'Demo',
                'telefono' => '999888' . str_pad($i, 3, '0', STR_PAD_LEFT)
            ]);
        }
        $clientes = Cliente::all();

        // 2. Proveedores (20 registros)
        for ($i = 101; $i <= 120; $i++) {
            Proveedor::firstOrCreate(['ruc' => '2050' . str_pad($i, 4, '0', STR_PAD_LEFT)], [
                'razon_social' => 'Proveedor Nuevo ' . $i . ' S.A.C',
                'contacto' => 'Vendedor ' . $i,
                'telefono' => '012345' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => 'ventas' . $i . '@demo.com'
            ]);
        }
        $proveedores = Proveedor::all();
        
        // 3. Abrir Caja
        $caja = Caja::firstOrCreate(
            ['user_id' => $admin->id, 'estado' => 'abierta'],
            ['apertura' => Carbon::now(), 'monto_apertura' => 100.00]
        );

        // Pre-calcular últimos 5 meses
        $meses = [];
        for ($i = 0; $i < 5; $i++) {
            $meses[] = Carbon::now()->subMonths($i);
        }

        // 4. Crear 20 Ventas (últimos 5 meses)
        for ($v = 1; $v <= 20; $v++) {
            $mes = $meses[array_rand($meses)]->copy()->startOfMonth();
            $venta = Venta::create([
                'codigo' => 'V-DEMO5-' . $v . '-' . rand(100, 999),
                'cliente_id' => $clientes->random()->id,
                'user_id' => $admin->id,
                'caja_id' => $caja->id,
                'tipo_comprobante' => 'Boleta',
                'serie' => 'B001',
                'numero' => rand(10000, 99999),
                'subtotal' => 0,
                'descuento' => 0,
                'impuesto' => 0,
                'total' => 0,
                'forma_pago' => 'efectivo',
                'estado' => 'emitida',
                'fecha' => $mes->addDays(rand(1, 25))
            ]);

            $totalVenta = 0;
            // Añadir detalles de venta (random 1 a 4 productos)
            for ($d = 0; $d < rand(1, 4); $d++) {
                $prod = $productos->random();
                $lote = Lote::where('producto_id', $prod->id)->first();
                $qty = rand(1, 5);
                $subt = $prod->precio_venta * $qty;
                if($lote) {
                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $prod->id,
                        'lote_id' => $lote->id,
                        'cantidad' => $qty,
                        'precio_unitario' => $prod->precio_venta,
                        'descuento' => 0,
                        'subtotal' => $subt
                    ]);
                    $totalVenta += $subt;
                }
            }
            
            $venta->update([
                'subtotal' => $totalVenta / 1.18,
                'impuesto' => $totalVenta - ($totalVenta / 1.18),
                'total' => $totalVenta
            ]);
        }

        // 5. Crear 20 Compras (últimos 5 meses)
        for ($c = 1; $c <= 20; $c++) {
            $mes = $meses[array_rand($meses)]->copy()->startOfMonth();
            $fechaCompra = $mes->addDays(rand(1, 25));

            $compra = Compra::create([
                'codigo' => 'C-DEMO5-' . $c . '-' . rand(100, 999),
                'proveedor_id' => $proveedores->random()->id,
                'user_id' => $admin->id,
                'estado' => 'recibida',
                'subtotal' => 0,
                'impuesto' => 0,
                'total' => 0,
                'fecha' => $fechaCompra,
                'fecha_recepcion' => $fechaCompra->copy()->addDays(rand(0, 3))
            ]);

            $totalCompra = 0;
            for ($d = 0; $d < rand(1, 3); $d++) {
                $prod = $productos->random();
                $qty = rand(10, 50);
                $subt = $prod->precio_compra * $qty;
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $prod->id,
                    'numero_lote' => 'LD5-' . rand(1000, 9999),
                    'fecha_vencimiento' => Carbon::now()->addDays(rand(30, 365)),
                    'cantidad' => $qty,
                    'precio_unitario' => $prod->precio_compra,
                    'subtotal' => $subt
                ]);
                
                // Generar lotes próximos a vencer a veces
                if (rand(1, 10) > 8) {
                    Lote::create([
                        'producto_id' => $prod->id,
                        'numero_lote' => 'LD5-V-' . rand(1000, 9999),
                        'fecha_vencimiento' => Carbon::now()->addDays(rand(10, 80)),
                        'cantidad' => $qty
                    ]);
                }
                
                $totalCompra += $subt;
            }
            $compra->update([
                'subtotal' => $totalCompra / 1.18,
                'impuesto' => $totalCompra - ($totalCompra / 1.18),
                'total' => $totalCompra
            ]);
        }

        // 6. Crear 20 Recetas (últimos 5 meses)
        for ($r = 1; $r <= 20; $r++) {
            $mes = $meses[array_rand($meses)]->copy()->startOfMonth();
            $receta = Receta::create([
                'codigo' => 'R-DEMO5-' . $r . '-' . rand(100, 999),
                'cliente_id' => $clientes->random()->id,
                'user_id' => $admin->id,
                'medico' => 'Dr. Especialista ' . $r,
                'especialidad' => 'Especialidad ' . rand(1, 5),
                'cmp' => rand(10000, 99999),
                'fecha' => $mes->addDays(rand(1, 25)),
                'retenida' => (bool)rand(0, 1),
                'diagnostico' => 'Diagnostico Nuevo ' . $r
            ]);

            DetalleReceta::create([
                'receta_id' => $receta->id,
                'producto_id' => $productos->random()->id,
                'cantidad' => rand(1, 4),
                'indicaciones' => 'Tomar según indicación médica'
            ]);
        }
    }
}
