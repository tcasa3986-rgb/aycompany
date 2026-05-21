<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orden;
use App\Models\Paciente;
use App\Models\Prueba;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        $ordenesHoy = Orden::whereDate('fecha_registro', $hoy)->count();
        $pacientesTotal = Paciente::count();
        $ingresosHoy = Orden::whereDate('fecha_registro', $hoy)->where('pagado', true)->sum('total');
        
        $ordenesPendientes = Orden::whereIn('estado', ['Pendiente', 'En proceso'])->count();

        // Datos para gráfico de barras (Órdenes por mes) y de área (Ingresos por mes)
        $meses = [];
        $ordenesPorMes = [];
        $ingresosPorMes = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::today()->startOfMonth()->subMonths($i);
            $meses[] = $mes->translatedFormat('M');
            
            $ordenesPorMes[] = Orden::whereMonth('fecha_registro', $mes->month)->whereYear('fecha_registro', $mes->year)->count();
            $ingresosPorMes[] = Orden::whereMonth('fecha_registro', $mes->month)->whereYear('fecha_registro', $mes->year)->where('pagado', true)->sum('total');
        }

        // Datos para Dona (Estado de Órdenes del mes actual)
        $estados = DB::table('ordenes')
            ->select('estado', DB::raw('count(*) as total'))
            ->whereMonth('fecha_registro', $hoy->month)
            ->groupBy('estado')
            ->pluck('total', 'estado')->toArray();

        // Top 5 Pruebas más solicitadas
        $topPruebas = DB::table('orden_detalles')
            ->join('pruebas', 'orden_detalles.prueba_id', '=', 'pruebas.id')
            ->select('pruebas.nombre', DB::raw('count(*) as total'))
            ->groupBy('pruebas.id', 'pruebas.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'nombre')->toArray();

        $ordenesRecientes = Orden::with(['paciente'])->latest('fecha_registro')->take(5)->get();

        return view('dashboard.index', compact(
            'ordenesHoy', 'pacientesTotal', 'ingresosHoy', 'ordenesPendientes',
            'meses', 'ordenesPorMes', 'ingresosPorMes', 'estados', 'topPruebas', 'ordenesRecientes'
        ));
    }
}
