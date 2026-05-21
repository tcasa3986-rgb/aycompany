<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Pago;
use App\Models\Repartidor;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function ventas(Request $request)
    {
        $desde = $request->filled('desde') ? Carbon::parse($request->desde) : Carbon::now()->startOfMonth();
        $hasta = $request->filled('hasta') ? Carbon::parse($request->hasta) : Carbon::now();

        $pedidos = Pedido::with(['cliente', 'repartidor'])
            ->whereBetween('created_at', [$desde->startOfDay(), $hasta->endOfDay()])
            ->when($request->filled('estado'), fn($q) => $q->where('estado', $request->estado))
            ->get();

        $resumen = [
            'total_pedidos'    => $pedidos->count(),
            'pedidos_entregados' => $pedidos->where('estado', 'entregado')->count(),
            'pedidos_cancelados' => $pedidos->where('estado', 'cancelado')->count(),
            'total_ingresos'   => $pedidos->where('estado', 'entregado')->sum('total'),
            'ticket_promedio'  => $pedidos->where('estado', 'entregado')->avg('total') ?? 0,
            'total_delivery'   => $pedidos->where('estado', 'entregado')->sum('costo_delivery'),
        ];

        // Agrupar por día
        $porDia = $pedidos->where('estado', 'entregado')
            ->groupBy(fn($p) => $p->created_at->format('d/m/Y'))
            ->map(fn($g) => ['cantidad' => $g->count(), 'ingresos' => $g->sum('total')]);

        return view('reportes.ventas', compact('pedidos', 'resumen', 'porDia', 'desde', 'hasta'));
    }

    public function repartidores(Request $request)
    {
        $desde = $request->filled('desde') ? Carbon::parse($request->desde) : Carbon::now()->startOfMonth();
        $hasta = $request->filled('hasta') ? Carbon::parse($request->hasta) : Carbon::now();

        $repartidores = Repartidor::withCount(['entregas as entregas_periodo' => function ($q) use ($desde, $hasta) {
            $q->whereBetween('created_at', [$desde, $hasta])->where('estado', 'entregado');
        }])
        ->with(['entregas' => function ($q) use ($desde, $hasta) {
            $q->whereBetween('created_at', [$desde, $hasta])->where('estado', 'entregado');
        }])
        ->get()
        ->map(function ($r) {
            $r->tiempo_promedio   = $r->entregas->avg('tiempo_minutos');
            $r->calificacion_prom = $r->entregas->whereNotNull('calificacion')->avg('calificacion');
            return $r;
        })
        ->sortByDesc('entregas_periodo');

        return view('reportes.repartidores', compact('repartidores', 'desde', 'hasta'));
    }

    public function clientes(Request $request)
    {
        $desde = $request->filled('desde') ? Carbon::parse($request->desde) : Carbon::now()->startOfMonth();
        $hasta = $request->filled('hasta') ? Carbon::parse($request->hasta) : Carbon::now();

        $clientes = Cliente::withCount(['pedidos as pedidos_periodo' => function ($q) use ($desde, $hasta) {
            $q->whereBetween('created_at', [$desde, $hasta]);
        }])
        ->withSum(['pedidos as gasto_periodo' => function ($q) use ($desde, $hasta) {
            $q->whereBetween('created_at', [$desde, $hasta])->where('estado', 'entregado');
        }], 'total')
        ->having('pedidos_periodo', '>', 0)
        ->orderByDesc('gasto_periodo')
        ->get();

        return view('reportes.clientes', compact('clientes', 'desde', 'hasta'));
    }
}
