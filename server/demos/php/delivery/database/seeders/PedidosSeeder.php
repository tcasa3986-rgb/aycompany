<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Repartidor;
use App\Models\Pago;
use App\Models\Entrega;
use App\Models\Zona;
use App\Models\User;
use Carbon\Carbon;

class PedidosSeeder extends Seeder
{
    /**
     * Genera ~80 pedidos en los últimos 30 días, distribuidos para
     * que el dashboard muestre tendencias y graficas reales.
     */
    public function run(): void
    {
        $clientes     = Cliente::all();
        $productos    = Producto::all();
        $repartidores = Repartidor::where('activo', true)->get();
        $zonas        = Zona::activas()->get();
        $operador     = User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin','operador','super-admin']))->first();

        if ($clientes->isEmpty() || $productos->isEmpty()) {
            $this->command->warn('No hay clientes o productos. Saltando PedidosSeeder.');
            return;
        }

        $estadosFlujo = ['entregado','entregado','entregado','entregado','entregado','en_camino','listo','preparando','confirmado','pendiente','cancelado'];
        $tiposPago    = ['efectivo','yape','plin','tarjeta','transferencia'];
        $contador     = 1;

        // Generar pedidos repartidos en 30 días, con más densidad en días recientes
        for ($d = 29; $d >= 0; $d--) {
            // 1 a 5 pedidos por día (más en fines de semana)
            $base   = Carbon::now()->subDays($d);
            $cantidadDia = $base->isWeekend() ? rand(3, 5) : rand(1, 4);

            for ($k = 0; $k < $cantidadDia; $k++) {
                $cliente = $clientes->random();
                $zona    = $zonas->isNotEmpty() ? $zonas->random() : null;

                $estado = $estadosFlujo[array_rand($estadosFlujo)];
                // Pedidos antiguos siempre están entregados o cancelados
                if ($d > 7 && in_array($estado, ['pendiente','confirmado','preparando','listo','en_camino'])) {
                    $estado = 'entregado';
                }

                $tipoPago = $tiposPago[array_rand($tiposPago)];
                $itemsDelPedido = rand(1, 4);
                $subtotal = 0;
                $itemsData = [];

                for ($i = 0; $i < $itemsDelPedido; $i++) {
                    $prod = $productos->random();
                    $cant = rand(1, 3);
                    $precio = (float) ($prod->precio_delivery ?? $prod->precio);
                    $sub = $precio * $cant;
                    $subtotal += $sub;
                    $itemsData[] = [
                        'producto_id'     => $prod->id,
                        'nombre_producto' => $prod->nombre,
                        'precio_unitario' => $precio,
                        'cantidad'        => $cant,
                        'subtotal'        => $sub,
                        'notas'           => rand(0,3) === 0 ? 'Sin cebolla' : null,
                    ];
                }

                $costoDelivery = $zona ? (float) $zona->costo_delivery : (float) rand(5, 12);
                $descuento = rand(0, 4) === 0 ? round($subtotal * 0.1, 2) : 0;
                $total = $subtotal + $costoDelivery - $descuento;

                $createdAt = $base->copy()->setTime(rand(11,22), rand(0,59), rand(0,59));
                $repartidor = in_array($estado, ['en_camino','entregado']) && $repartidores->isNotEmpty()
                    ? $repartidores->random() : null;

                $pedido = Pedido::create([
                    'numero'             => 'PED-' . str_pad($contador++, 6, '0', STR_PAD_LEFT),
                    'cliente_id'         => $cliente->id,
                    'user_id'            => $operador?->id,
                    'repartidor_id'      => $repartidor?->id,
                    'zona_id'            => $zona?->id,
                    'direccion_entrega'  => $cliente->direccion ?? 'Av. Central 123',
                    'distrito_entrega'   => $zona?->distrito ?? $cliente->distrito,
                    'tipo_pago'          => $tipoPago,
                    'estado'             => $estado,
                    'estado_pago'        => in_array($estado, ['entregado']) ? 'pagado' : 'pendiente',
                    'costo_delivery'     => $costoDelivery,
                    'descuento'          => $descuento,
                    'subtotal'           => $subtotal,
                    'total'              => $total,
                    'notas'              => null,
                    'fecha_entrega'      => $estado === 'entregado' ? $createdAt->copy()->addMinutes(rand(20, 75)) : null,
                    'created_at'         => $createdAt,
                    'updated_at'         => $createdAt,
                ]);

                foreach ($itemsData as $item) {
                    PedidoItem::create(array_merge($item, [
                        'pedido_id'  => $pedido->id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]));
                }

                // Pago si está entregado
                if ($estado === 'entregado') {
                    Pago::create([
                        'pedido_id'      => $pedido->id,
                        'registrado_por' => $operador?->id,
                        'metodo'         => $tipoPago,
                        'monto'          => $total,
                        'estado'         => 'completado',
                        'fecha_pago'     => $pedido->fecha_entrega ?? $createdAt,
                        'referencia'     => $tipoPago === 'tarjeta' ? 'TXN'.rand(100000,999999) : null,
                        'created_at'     => $pedido->fecha_entrega ?? $createdAt,
                        'updated_at'     => $pedido->fecha_entrega ?? $createdAt,
                    ]);
                }

                // Entrega si tiene repartidor
                if ($repartidor) {
                    Entrega::create([
                        'pedido_id'             => $pedido->id,
                        'repartidor_id'         => $repartidor->id,
                        'asignado_por'          => $operador?->id,
                        'estado'                => $estado === 'entregado' ? 'entregado' : ($estado === 'en_camino' ? 'en_camino' : 'asignado'),
                        'fecha_asignacion'      => $createdAt,
                        'fecha_recogida'        => $estado === 'entregado' ? $createdAt->copy()->addMinutes(10) : null,
                        'fecha_entrega_estimada'=> $createdAt->copy()->addMinutes(45),
                        'fecha_entrega_real'    => $pedido->fecha_entrega,
                        'distancia_km'          => round(rand(10,80) / 10, 2),
                        'tiempo_minutos'        => $estado === 'entregado' ? rand(20, 70) : null,
                        'calificacion'          => $estado === 'entregado' ? round(rand(35, 50) / 10, 1) : null,
                        'created_at'            => $createdAt,
                        'updated_at'            => $createdAt,
                    ]);
                }
            }
        }

        $this->command->info('Pedidos generados con 30 días de historial. Total: ' . Pedido::count());
    }
}
