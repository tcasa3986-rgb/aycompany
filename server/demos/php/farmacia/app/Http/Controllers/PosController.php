<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('nombres')->limit(200)->get(['id', 'documento', 'nombres', 'apellidos', 'puntos_fidelidad']);
        $cajaAbierta = Caja::where('user_id', Auth::id())->where('estado', 'abierta')->first();
        return view('pos.index', compact('clientes', 'cajaAbierta'));
    }

    public function buscar(Request $request)
    {
        $q = $request->get('q', '');
        $sucursalId = auth()->user()->current_sucursal_id;

        $productos = Producto::where('productos.activo', true)
            ->join('sucursal_producto', 'productos.id', '=', 'sucursal_producto.producto_id')
            ->where('sucursal_producto.sucursal_id', $sucursalId)
            ->where(function ($w) use ($q) {
                $w->where('productos.nombre', 'like', "%$q%")
                  ->orWhere('productos.codigo', 'like', "%$q%")
                  ->orWhere('productos.principio_activo', 'like', "%$q%");
            })
            ->where('sucursal_producto.stock', '>', 0)
            ->limit(15)
            ->get([
                'productos.id', 
                'productos.codigo', 
                'productos.nombre', 
                'productos.principio_activo', 
                'productos.concentracion', 
                'productos.precio_venta', 
                'sucursal_producto.stock'
            ]);

        return response()->json($productos);
    }

    public function store(Request $request)
    {
        $caja = Caja::where('user_id', Auth::id())->where('estado', 'abierta')->first();
        if (! $caja) {
            return response()->json([
                'message' => 'No tienes una caja abierta. Abre tu caja antes de cobrar.',
            ], 422);
        }

        $data = $request->validate([
            'cliente_id'        => ['nullable', 'exists:clientes,id'],
            'forma_pago'        => ['required', 'in:efectivo,tarjeta,transferencia,mixto,credito'],
            'pago_recibido'     => ['required', 'numeric', 'min:0'],
            'descuento'         => ['nullable', 'numeric', 'min:0'],
            'puntos_canje'      => ['nullable', 'integer', 'min:0'],
            'observaciones'     => ['nullable', 'string', 'max:500'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'exists:productos,id'],
            'items.*.cantidad'    => ['required', 'integer', 'min:1'],
            'items.*.precio'      => ['required', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($data, $caja) {
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['cantidad'] * $item['precio'];
            }

            $descuentoPuntos = 0;
            $puntosCanjeados = (int) ($data['puntos_canje'] ?? 0);

            if ($puntosCanjeados > 0 && ! empty($data['cliente_id'])) {
                $cliente = Cliente::find($data['cliente_id']);
                if ($cliente && $cliente->puntos_fidelidad >= $puntosCanjeados) {
                    $descuentoPuntos = $puntosCanjeados; // 1 punto = S/ 1.00
                } else {
                    return response()->json(['message' => 'Puntos insuficientes.'], 422);
                }
            }

            $descuentoTotal = (float) ($data['descuento'] ?? 0) + $descuentoPuntos;
            $base           = max(0, $subtotal - $descuentoTotal);
            $impuesto       = round($base * 0.18, 2);
            $total          = round($base + $impuesto, 2);
            $cambio         = max(0, ((float) $data['pago_recibido']) - $total);

            $venta = Venta::create([
                'codigo'           => 'V-' . now()->format('YmdHis'),
                'cliente_id'       => $data['cliente_id'] ?? null,
                'user_id'          => Auth::id(),
                'caja_id'          => $caja->id,
                'tipo_comprobante' => 'boleta',
                'subtotal'         => $subtotal,
                'descuento'        => $descuentoTotal,
                'impuesto'         => $impuesto,
                'total'            => $total,
                'forma_pago'       => $data['forma_pago'],
                'pago_recibido'    => $data['forma_pago'] === 'credito' ? 0 : $data['pago_recibido'],
                'cambio'           => $data['forma_pago'] === 'credito' ? 0 : $cambio,
                'puntos_canjeados' => $puntosCanjeados,
                'estado'           => 'emitida',
                'observaciones'    => $data['observaciones'] ?? null,
                'fecha'            => now(),
            ]);

            // Si es crédito, aumentar deuda del cliente
            if ($data['forma_pago'] === 'credito') {
                if (empty($data['cliente_id'])) {
                    return response()->json(['message' => 'Venta al crédito requiere seleccionar un cliente.'], 422);
                }
                
                $cliente = Cliente::find($data['cliente_id']);
                if (($cliente->saldo_deudor + $total) > $cliente->limite_credito) {
                    return response()->json([
                        'message' => "Límite de crédito excedido. Disponible: " . ($cliente->limite_credito - $cliente->saldo_deudor)
                    ], 422);
                }

                $cliente->increment('saldo_deudor', $total);
            }

            foreach ($data['items'] as $item) {
                DetalleVenta::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal'        => $item['cantidad'] * $item['precio'],
                ]);

                // Descontar stock de la sucursal actual
                DB::table('sucursal_producto')
                    ->where('sucursal_id', $venta->sucursal_id)
                    ->where('producto_id', $item['producto_id'])
                    ->decrement('stock', $item['cantidad']);
            }

            // Actualizar puntos del cliente: restar canjeados y sumar nuevos
            if (! empty($data['cliente_id'])) {
                $puntosGanados = (int) floor($total / 10);
                $puntosUsados  = (int) ($data['puntos_canje'] ?? 0);
                $delta = $puntosGanados - $puntosUsados;

                Cliente::where('id', $data['cliente_id'])
                    ->update(['puntos_fidelidad' => DB::raw("GREATEST(puntos_fidelidad + ($delta), 0)")]);
            }

            return response()->json([
                'ok'         => true,
                'venta_id'   => $venta->id,
                'codigo'     => $venta->codigo,
                'total'      => $venta->total,
                'cambio'     => $venta->cambio,
                'redirect'   => route('ventas.show', $venta),
                'ticket_url' => route('ventas.ticket', $venta),
            ]);
        });
    }
}
