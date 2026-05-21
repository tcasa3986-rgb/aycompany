<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Repartidor;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        // KPIs principales
        $kpis = [
            'pedidos_hoy'        => Pedido::whereDate('created_at', $hoy)->count(),
            'pedidos_pendientes' => Pedido::whereIn('estado', ['pendiente', 'confirmado', 'preparando'])->count(),
            'en_camino'          => Pedido::where('estado', 'en_camino')->count(),
            'entregados_hoy'     => Pedido::whereDate('created_at', $hoy)->where('estado', 'entregado')->count(),
            'ingresos_hoy'       => Pago::whereDate('created_at', $hoy)->where('estado', 'completado')->sum('monto'),
            'ingresos_mes'       => Pago::whereMonth('created_at', $hoy->month)->whereYear('created_at', $hoy->year)->where('estado', 'completado')->sum('monto'),
            'clientes_total'     => Cliente::activos()->count(),
            'repartidores_disp'  => Repartidor::disponibles()->count(),
        ];

        // Pedidos últimos 7 días (para gráfica)
        $pedidos7dias = [];
        $labels7dias  = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $labels7dias[]  = $fecha->format('d/m');
            $pedidos7dias[] = Pedido::whereDate('created_at', $fecha)->count();
        }

        // Ingresos últimos 7 días
        $ingresos7dias = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $ingresos7dias[] = Pago::whereDate('created_at', $fecha)->where('estado', 'completado')->sum('monto');
        }

        // Distribución de estados de pedidos
        $estadosPedidos = Pedido::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // Últimos pedidos
        $ultimosPedidos = Pedido::with(['cliente', 'repartidor'])
            ->latest()
            ->limit(8)
            ->get();

        // Top repartidores del mes
        $topRepartidores = Repartidor::withCount(['entregas' => function ($q) use ($hoy) {
            $q->whereMonth('created_at', $hoy->month)->where('estado', 'entregado');
        }])
        ->orderByDesc('entregas_count')
        ->limit(5)
        ->get();

        return view('dashboard.index', compact(
            'kpis', 'labels7dias', 'pedidos7dias', 'ingresos7dias',
            'estadosPedidos', 'ultimosPedidos', 'topRepartidores'
        ));
    }
}
