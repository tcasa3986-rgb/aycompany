<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Pedido;
use App\Models\Repartidor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EntregaController extends Controller
{
    public function index(Request $request)
    {
        $query = Entrega::with(['pedido.cliente', 'repartidor']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('repartidor_id')) {
            $query->where('repartidor_id', $request->repartidor_id);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_asignacion', $request->fecha);
        }

        $entregas     = $query->latest()->paginate(20)->withQueryString();
        $repartidores = Repartidor::activos()->orderBy('nombre')->get();

        return view('entregas.index', compact('entregas', 'repartidores'));
    }

    public function asignar(Request $request)
    {
        $request->validate([
            'pedido_id'               => 'required|exists:pedidos,id',
            'repartidor_id'           => 'required|exists:repartidores,id',
            'fecha_entrega_estimada'  => 'nullable|date',
            'observaciones'           => 'nullable|string',
        ]);

        $pedido      = Pedido::findOrFail($request->pedido_id);
        $repartidor  = Repartidor::findOrFail($request->repartidor_id);

        $entrega = Entrega::create([
            'pedido_id'              => $pedido->id,
            'repartidor_id'          => $repartidor->id,
            'asignado_por'           => auth()->id(),
            'estado'                 => 'asignado',
            'fecha_asignacion'       => now(),
            'fecha_entrega_estimada' => $request->fecha_entrega_estimada ?? Carbon::now()->addMinutes(45),
            'observaciones'          => $request->observaciones,
        ]);

        // Actualizar pedido y repartidor
        $pedido->update(['repartidor_id' => $repartidor->id, 'estado' => 'en_camino']);
        $repartidor->update(['estado' => 'ocupado']);

        return back()->with('success', "Pedido asignado a {$repartidor->nombre_completo}.");
    }

    public function actualizarEstado(Request $request, Entrega $entrega)
    {
        $request->validate([
            'estado'       => 'required|in:asignado,recogido,en_camino,entregado,fallido,devuelto',
            'observaciones'=> 'nullable|string',
        ]);

        $datos = ['estado' => $request->estado, 'observaciones' => $request->observaciones];

        if ($request->estado === 'recogido') {
            $datos['fecha_recogida'] = now();
        } elseif ($request->estado === 'entregado') {
            $datos['fecha_entrega_real'] = now();
            $minutos = $entrega->fecha_asignacion
                ? $entrega->fecha_asignacion->diffInMinutes(now())
                : null;
            $datos['tiempo_minutos'] = $minutos;

            // Actualizar pedido y repartidor
            $entrega->pedido->update(['estado' => 'entregado', 'fecha_entrega' => now()]);
            $entrega->repartidor->update(['estado' => 'disponible']);
            $entrega->repartidor->increment('total_entregas');
        } elseif (in_array($request->estado, ['fallido', 'devuelto'])) {
            $entrega->repartidor->update(['estado' => 'disponible']);
        }

        $entrega->update($datos);

        return back()->with('success', 'Estado de entrega actualizado.');
    }
}
