<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\MovimientoCaja;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $ventas = Venta::with(['cliente', 'cajero'])
            ->orderByDesc('fecha')
            ->paginate(20);
        return view('ventas.index', compact('ventas'));
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'cajero', 'detalles.producto']);
        return view('ventas.show', compact('venta'));
    }

    public function ticket(Venta $venta)
    {
        $venta->load(['cliente', 'cajero', 'detalles.producto']);
        return view('ventas.ticket', compact('venta'));
    }

    public function anular(Request $request, Venta $venta)
    {
        if ($venta->estado !== 'emitida') {
            return back()->with('error', 'Solo se pueden anular ventas emitidas.');
        }

        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
            'tipo'   => ['required', 'in:anulada,devuelta'],
        ]);

        DB::transaction(function () use ($venta, $data) {
            $venta->load('detalles');

            // Reponer stock
            foreach ($venta->detalles as $det) {
                DB::table('sucursal_producto')
                    ->where('sucursal_id', $venta->sucursal_id)
                    ->where('producto_id', $det->producto_id)
                    ->increment('stock', $det->cantidad);
            }

            // Devolver puntos canjeados al cliente y restar puntos ganados
            if ($venta->cliente_id) {
                $puntosCanjeados = (int) $venta->puntos_canjeados;
                $puntosGanados   = (int) floor($venta->total / 10);
                $delta = $puntosCanjeados - $puntosGanados; // se devuelven los canjeados, se quitan los ganados
                if ($delta !== 0) {
                    Cliente::where('id', $venta->cliente_id)
                        ->update(['puntos_fidelidad' => DB::raw("GREATEST(puntos_fidelidad + ($delta), 0)")]);
                }
            }

            // Registrar egreso de caja si la venta fue en efectivo y caja sigue abierta
            if ($venta->caja_id && $venta->forma_pago === 'efectivo') {
                $caja = $venta->caja;
                if ($caja && $caja->estado === 'abierta') {
                    MovimientoCaja::create([
                        'caja_id'  => $caja->id,
                        'tipo'     => 'egreso',
                        'monto'    => $venta->total,
                        'concepto' => "Devolución venta {$venta->codigo} — {$data['motivo']}",
                    ]);
                }
            }

            $venta->update([
                'estado'           => $data['tipo'],
                'motivo_anulacion' => $data['motivo'],
                'anulada_at'       => now(),
                'anulada_por'      => Auth::id(),
            ]);
        });

        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Venta ' . $data['tipo'] . ' correctamente. Stock repuesto.');
    }
}
