<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entrega;
use App\Models\Repartidor;
use Illuminate\Http\Request;

class RepartidorApiController extends Controller
{
    private function rep(Request $request): Repartidor
    {
        $rep = Repartidor::where('user_id', $request->user()->id)->first();
        abort_unless($rep, 403, 'Tu usuario no está vinculado a un repartidor.');
        return $rep;
    }

    public function entregas(Request $request)
    {
        $rep = $this->rep($request);
        $items = Entrega::with('pedido.cliente','pedido.items')
            ->where('repartidor_id', $rep->id)
            ->whereIn('estado', $request->input('estado_in', ['asignado','recogido','en_camino']))
            ->latest('fecha_asignacion')
            ->get()
            ->map(fn($e) => [
                'id'        => $e->id,
                'estado'    => $e->estado,
                'asignada'  => $e->fecha_asignacion?->toIso8601String(),
                'pedido'    => [
                    'numero'    => $e->pedido->numero,
                    'total'     => $e->pedido->total,
                    'tipo_pago' => $e->pedido->tipo_pago,
                    'direccion' => $e->pedido->direccion_entrega,
                    'referencia'=> $e->pedido->referencia_entrega,
                    'cliente'   => [
                        'nombre'   => $e->pedido->cliente->nombre,
                        'telefono' => $e->pedido->cliente->telefono,
                    ],
                    'items'     => $e->pedido->items->map(fn($i) => [
                        'cantidad' => $i->cantidad,
                        'producto' => $i->nombre_producto,
                        'subtotal' => $i->subtotal,
                        'notas'    => $i->notas,
                    ]),
                ],
            ]);
        return response()->json(['data' => $items]);
    }

    public function actualizarEntrega(Request $request, Entrega $entrega)
    {
        $rep = $this->rep($request);
        abort_unless($entrega->repartidor_id === $rep->id, 403);

        $request->validate([
            'estado'        => 'required|in:recogido,en_camino,entregado,fallido',
            'lat'           => 'nullable|numeric',
            'lng'           => 'nullable|numeric',
            'observaciones' => 'nullable|string',
        ]);

        $datos = ['estado' => $request->estado];
        if ($request->observaciones) $datos['observaciones'] = $request->observaciones;
        if ($request->estado === 'recogido' && !$entrega->fecha_recogida) $datos['fecha_recogida'] = now();
        if ($request->estado === 'entregado') {
            $datos['fecha_entrega_real'] = now();
            if ($request->lat && $request->lng) {
                $datos['lat_entrega'] = $request->lat;
                $datos['lng_entrega'] = $request->lng;
            }
        }
        $entrega->update($datos);

        if ($entrega->pedido) {
            if ($request->estado === 'en_camino') {
                $entrega->pedido->update(['estado' => 'en_camino']);
            } elseif ($request->estado === 'entregado') {
                $entrega->pedido->update(['estado' => 'entregado', 'fecha_entrega' => now()]);
                $rep->update(['estado' => 'disponible']);
            }
            try { app(\App\Services\NotificacionService::class)->cambioEstadoPedido($entrega->pedido); } catch (\Throwable $e) {}
        }

        return response()->json(['ok' => true, 'estado' => $entrega->estado]);
    }

    public function ubicacion(Request $request)
    {
        $rep = $this->rep($request);
        $request->validate(['lat' => 'required|numeric', 'lng' => 'required|numeric']);
        $rep->update([
            'lat_actual'          => $request->lat,
            'lng_actual'          => $request->lng,
            'ultima_ubicacion_at' => now(),
        ]);
        return response()->json(['ok' => true]);
    }
}
