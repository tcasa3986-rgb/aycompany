<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pago::with(['pedido.cliente', 'registradoPor']);

        if ($request->filled('metodo'))   $query->where('metodo', $request->metodo);
        if ($request->filled('estado'))   $query->where('estado', $request->estado);
        if ($request->filled('fecha_desde')) $query->whereDate('fecha_pago', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta')) $query->whereDate('fecha_pago', '<=', $request->fecha_hasta);

        $pagos    = $query->latest()->paginate(20)->withQueryString();
        $resumen  = [
            'total_hoy'  => Pago::whereDate('fecha_pago', today())->where('estado', 'completado')->sum('monto'),
            'total_mes'  => Pago::whereMonth('fecha_pago', now()->month)->where('estado', 'completado')->sum('monto'),
            'por_metodo' => Pago::where('estado', 'completado')
                ->selectRaw('metodo, SUM(monto) as total, COUNT(*) as cantidad')
                ->groupBy('metodo')
                ->get(),
        ];

        return view('pagos.index', compact('pagos', 'resumen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pedido_id'         => 'required|exists:pedidos,id',
            'metodo'            => 'required|in:efectivo,tarjeta,transferencia,yape,plin,otro',
            'monto'             => 'required|numeric|min:0.01',
            'vuelto'            => 'nullable|numeric|min:0',
            'referencia'        => 'nullable|string|max:60',
            'comprobante_tipo'  => 'nullable|in:boleta,factura,ninguno',
            'comprobante_numero'=> 'nullable|string|max:30',
            'notas'             => 'nullable|string',
        ]);

        $pedido = Pedido::findOrFail($request->pedido_id);

        $pago = Pago::create([
            'pedido_id'          => $pedido->id,
            'registrado_por'     => auth()->id(),
            'metodo'             => $request->metodo,
            'monto'              => $request->monto,
            'vuelto'             => $request->vuelto ?? 0,
            'referencia'         => $request->referencia,
            'comprobante_tipo'   => $request->comprobante_tipo,
            'comprobante_numero' => $request->comprobante_numero,
            'notas'              => $request->notas,
            'estado'             => 'completado',
            'fecha_pago'         => now(),
        ]);

        // Actualizar estado de pago del pedido
        $totalPagado = $pedido->pagos()->where('estado', 'completado')->sum('monto');
        if ($totalPagado >= $pedido->total) {
            $pedido->update(['estado_pago' => 'pagado']);
        } elseif ($totalPagado > 0) {
            $pedido->update(['estado_pago' => 'parcial']);
        }

        return back()->with('success', 'Pago registrado correctamente.');
    }
}
