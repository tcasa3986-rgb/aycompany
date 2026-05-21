<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Repartidor;
use Illuminate\Http\Request;

class RepartidorPanelController extends Controller
{
    /**
     * Resuelve el repartidor asociado al usuario logueado.
     */
    private function repartidorActual(): Repartidor
    {
        $rep = Repartidor::where('user_id', auth()->id())->first();
        abort_unless($rep, 403, 'No hay un repartidor vinculado a tu cuenta.');
        return $rep;
    }

    public function index()
    {
        $rep = $this->repartidorActual();

        $entregas = Entrega::with('pedido.cliente','pedido.items')
            ->where('repartidor_id', $rep->id)
            ->whereIn('estado', ['asignado','recogido','en_camino'])
            ->latest('fecha_asignacion')
            ->get();

        $hoy = Entrega::where('repartidor_id', $rep->id)
            ->whereDate('fecha_asignacion', today())
            ->where('estado','entregado')
            ->count();

        return view('repartidor_panel.index', compact('rep', 'entregas', 'hoy'));
    }

    public function detalle(Entrega $entrega)
    {
        $rep = $this->repartidorActual();
        abort_unless($entrega->repartidor_id === $rep->id, 403);
        $entrega->load('pedido.cliente','pedido.items.producto','pedido.pagos');
        return view('repartidor_panel.detalle', compact('entrega', 'rep'));
    }

    public function actualizarEstado(Request $request, Entrega $entrega)
    {
        $rep = $this->repartidorActual();
        abort_unless($entrega->repartidor_id === $rep->id, 403);

        $request->validate([
            'estado' => 'required|in:recogido,en_camino,entregado,fallido',
            'lat'    => 'nullable|numeric',
            'lng'    => 'nullable|numeric',
            'observaciones' => 'nullable|string|max:300',
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

        // Reflejar en pedido
        if ($entrega->pedido) {
            if ($request->estado === 'en_camino') {
                $entrega->pedido->update(['estado' => 'en_camino']);
            } elseif ($request->estado === 'entregado') {
                $entrega->pedido->update(['estado' => 'entregado', 'fecha_entrega' => now()]);
                $rep->update(['estado' => 'disponible']);
            }
            try { app(\App\Services\NotificacionService::class)->cambioEstadoPedido($entrega->pedido); } catch (\Throwable $e) {}
        }

        return back()->with('success', 'Estado actualizado.');
    }

    public function ubicacion(Request $request)
    {
        $rep = $this->repartidorActual();
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        $rep->update([
            'lat_actual'           => $request->lat,
            'lng_actual'           => $request->lng,
            'ultima_ubicacion_at'  => now(),
        ]);
        return response()->json(['ok' => true]);
    }
}
