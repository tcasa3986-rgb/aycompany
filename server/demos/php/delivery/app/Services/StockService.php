<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\MovimientoStock;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Registra un movimiento de stock y actualiza el stock del producto.
     */
    public static function registrar(Producto $producto, string $tipo, int $cantidad, array $extra = []): MovimientoStock
    {
        return DB::transaction(function () use ($producto, $tipo, $cantidad, $extra) {
            $producto->refresh();
            $stockAnterior = (int) $producto->stock;

            // 'entrada' y 'ajuste' positivo suman; 'salida' y 'merma' restan
            $delta = match ($tipo) {
                'entrada', 'ajuste' => $cantidad,
                'salida', 'merma'   => -abs($cantidad),
                default             => $cantidad,
            };

            $stockNuevo = max(0, $stockAnterior + $delta);
            $producto->update(['stock' => $stockNuevo]);

            return MovimientoStock::create([
                'producto_id'    => $producto->id,
                'user_id'        => auth()->id(),
                'pedido_id'      => $extra['pedido_id'] ?? null,
                'tipo'           => $tipo,
                'cantidad'       => $delta,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockNuevo,
                'costo_unitario' => $extra['costo_unitario'] ?? null,
                'motivo'         => $extra['motivo'] ?? null,
            ]);
        });
    }

    /**
     * Descuenta stock para todos los items de un pedido.
     */
    public static function descontarPedido(\App\Models\Pedido $pedido): void
    {
        foreach ($pedido->items as $item) {
            if ($item->producto) {
                self::registrar($item->producto, 'salida', $item->cantidad, [
                    'pedido_id' => $pedido->id,
                    'motivo'    => "Venta pedido {$pedido->numero}",
                ]);
            }
        }
    }
}
