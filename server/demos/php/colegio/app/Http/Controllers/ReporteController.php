<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Grado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        $anio = date('Y');

        $resumen = [
            'total_ingresos' => Pago::where('estado', 'pagado')->whereYear('fecha_pago', $anio)->sum('monto_pagado'),
            'total_pendiente'=> Pago::where('estado', 'pendiente')->sum('monto'),
            'total_vencido'  => Pago::where('estado', 'vencido')->sum('monto'),
            'total_alumnos'  => Alumno::where('estado', 'activo')->count(),
            'total_matriculas'=> Matricula::where('anio_escolar', $anio)->count(),
        ];

        // Ingresos por mes
        $ingresosMensuales = Pago::select(
                DB::raw('MONTH(fecha_pago) as mes'),
                DB::raw('SUM(monto_pagado) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->where('estado', 'pagado')
            ->whereYear('fecha_pago', $anio)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Alumnos por grado
        $alumnosPorGrado = Matricula::select('grado_id', DB::raw('COUNT(*) as total'))
            ->where('anio_escolar', $anio)
            ->where('estado', 'activo')
            ->with('grado')
            ->groupBy('grado_id')
            ->get();

        // Pagos por tipo de concepto
        $pagosPorTipo = Pago::join('conceptos_pago','pagos.concepto_id','=','conceptos_pago.id')
            ->select('conceptos_pago.tipo', DB::raw('SUM(pagos.monto_pagado) as total'), DB::raw('COUNT(*) as cantidad'))
            ->where('pagos.estado','pagado')
            ->whereYear('pagos.fecha_pago', $anio)
            ->groupBy('conceptos_pago.tipo')
            ->get();

        return view('reportes.index', compact('resumen', 'ingresosMensuales', 'alumnosPorGrado', 'pagosPorTipo', 'anio'));
    }

    public function pagos(Request $request)
    {
        $anio = $request->get('anio', date('Y'));
        $mes  = $request->get('mes');

        $query = Pago::with(['alumno', 'concepto'])->where('estado', 'pagado');

        if ($anio) $query->whereYear('fecha_pago', $anio);
        if ($mes)  $query->whereMonth('fecha_pago', $mes);

        $pagos = $query->latest('fecha_pago')->paginate(20)->withQueryString();

        $totalGeneral = $query->sum('monto_pagado');

        return view('reportes.pagos', compact('pagos', 'anio', 'mes', 'totalGeneral'));
    }

    public function alumnos(Request $request)
    {
        $anio    = $request->get('anio', date('Y'));
        $gradoId = $request->get('grado_id');

        $query = Matricula::with(['alumno', 'grado', 'seccion'])
            ->where('anio_escolar', $anio)
            ->where('estado', 'activo');

        if ($gradoId) $query->where('grado_id', $gradoId);

        $matriculas = $query->orderBy('grado_id')->orderBy('seccion_id')->paginate(25)->withQueryString();
        $grados     = Grado::all();

        return view('reportes.alumnos', compact('matriculas', 'grados', 'anio', 'gradoId'));
    }

    public function deudas(Request $request)
    {
        $alumnos = Alumno::where('estado', 'activo')
            ->whereHas('pagos', fn($q) => $q->where('estado', 'pendiente'))
            ->with(['pagos' => fn($q) => $q->where('estado', 'pendiente')->with('concepto')])
            ->get()
            ->map(function ($alumno) {
                $alumno->deuda_total = $alumno->pagos->sum('monto');
                return $alumno;
            })
            ->sortByDesc('deuda_total');

        $totalDeuda = $alumnos->sum('deuda_total');

        return view('reportes.deudas', compact('alumnos', 'totalDeuda'));
    }

    public function exportarCSV(Request $request, string $tipo)
    {
        $anio = $request->get('anio', date('Y'));

        switch ($tipo) {
            case 'pagos':
                $datos = Pago::with(['alumno', 'concepto'])
                    ->where('estado', 'pagado')
                    ->whereYear('fecha_pago', $anio)
                    ->get();

                $cabeceras = ['Recibo','Alumno','DNI','Concepto','Mes','Año','Monto','Método','Fecha'];
                $filas = $datos->map(fn($p) => [
                    $p->numero_recibo,
                    $p->alumno->nombre_completo ?? '',
                    $p->alumno->dni ?? '',
                    $p->concepto->nombre ?? '',
                    $p->nombre_mes,
                    $p->anio_escolar,
                    number_format($p->monto_pagado, 2),
                    $p->metodo_pago,
                    $p->fecha_pago?->format('d/m/Y'),
                ]);
                $filename = "reporte_pagos_{$anio}.csv";
                break;

            case 'alumnos':
                $datos = Matricula::with(['alumno', 'grado', 'seccion'])
                    ->where('anio_escolar', $anio)
                    ->where('estado', 'activo')
                    ->get();

                $cabeceras = ['Código','DNI','Nombres','Apellidos','Grado','Sección','Año'];
                $filas = $datos->map(fn($m) => [
                    $m->alumno->codigo ?? '',
                    $m->alumno->dni ?? '',
                    $m->alumno->nombres ?? '',
                    $m->alumno->apellidos ?? '',
                    $m->grado->nombre ?? '',
                    $m->seccion->nombre ?? '',
                    $m->anio_escolar,
                ]);
                $filename = "reporte_alumnos_{$anio}.csv";
                break;

            case 'deudas':
                $datos = Alumno::where('estado', 'activo')
                    ->whereHas('pagos', fn($q) => $q->where('estado', 'pendiente'))
                    ->with(['pagos' => fn($q) => $q->where('estado', 'pendiente')->with('concepto')])
                    ->get();

                $cabeceras = ['Código','DNI','Alumno','Concepto','Monto Deuda'];
                $filas = collect();
                foreach ($datos as $alumno) {
                    foreach ($alumno->pagos as $pago) {
                        $filas->push([
                            $alumno->codigo,
                            $alumno->dni,
                            $alumno->nombre_completo,
                            $pago->concepto->nombre ?? '',
                            number_format($pago->monto, 2),
                        ]);
                    }
                }
                $filename = "reporte_deudas.csv";
                break;

            default:
                return redirect()->route('reportes.index');
        }

        // Generar CSV
        $csv = implode(',', $cabeceras) . "\n";
        foreach ($filas as $fila) {
            $csv .= implode(',', array_map(fn($v) => '"'.str_replace('"','""',$v).'"', $fila)) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
