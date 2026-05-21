<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Pago;
use App\Models\Personal;
use App\Models\Mensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $anioActual = date('Y');

        // Estadísticas principales
        $stats = [
            'total_alumnos'     => Alumno::where('estado', 'activo')->count(),
            'total_matriculas'  => Matricula::where('anio_escolar', $anioActual)->where('estado', 'activo')->count(),
            'total_personal'    => Personal::where('estado', 'activo')->count(),
            'pagos_pendientes'  => Pago::where('estado', 'pendiente')->count(),
            'ingresos_mes'      => Pago::where('estado', 'pagado')
                                      ->whereMonth('fecha_pago', date('m'))
                                      ->whereYear('fecha_pago', $anioActual)
                                      ->sum('monto_pagado'),
            'deuda_total'       => Pago::where('estado', 'pendiente')->sum('monto'),
            'mensajes_no_leidos'=> Mensaje::where('destinatario_id', auth()->id())->where('leido', false)->count(),
        ];

        // Pagos por mes (últimos 6 meses)
        $pagosMensuales = Pago::select(
                DB::raw('MONTH(fecha_pago) as mes'),
                DB::raw('SUM(monto_pagado) as total')
            )
            ->where('estado', 'pagado')
            ->whereYear('fecha_pago', $anioActual)
            ->groupByRaw('MONTH(fecha_pago)')
            ->orderByRaw('MONTH(fecha_pago) asc')
            ->get()
            ->keyBy('mes');

        // Últimos pagos registrados
        $ultimosPagos = Pago::with(['alumno', 'concepto'])
            ->latest()
            ->take(8)
            ->get();

        // Últimas matrículas
        $ultimasMatriculas = Matricula::with(['alumno', 'grado', 'seccion'])
            ->latest()
            ->take(5)
            ->get();

        // Distribución de alumnos por grado
        $alumnosPorGrado = Matricula::select('grado_id', DB::raw('COUNT(*) as total'))
            ->where('anio_escolar', $anioActual)
            ->where('estado', 'activo')
            ->with('grado')
            ->groupBy('grado_id')
            ->get();

        return view('dashboard.index', compact(
            'stats', 'pagosMensuales', 'ultimosPagos',
            'ultimasMatriculas', 'alumnosPorGrado'
        ));
    }
}
