<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Empleado;
use App\Models\Asignacion;
use App\Models\Reparacion;
use App\Models\Baja;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    /**
     * Mostrar página de reportes
     */
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * Reporte de inventario de equipos
     */
    public function equipos(Request $request)
    {
        $user = auth()->user();

        $query = Equipo::with(['sucursal', 'tipoEquipo', 'marca', 'modelo']);

        if (!$user->isAdmin() && $user->id_sucursal) {
            $query->where('id_sucursal', $user->id_sucursal);
        }

        if ($request->id_sucursal) {
            $query->where('id_sucursal', $request->id_sucursal);
        }

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        $equipos = $query->orderBy('codigo_inventario')->get();

        $pdf = Pdf::loadView('pdf.equipos', compact('equipos'));
        return $pdf->download('reporte_equipos_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Reporte de asignaciones
     */
    public function asignaciones(Request $request)
    {
        $user = auth()->user();

        $query = Asignacion::with(['equipo.marca', 'equipo.modelo', 'empleado']);

        if (!$user->isAdmin() && $user->id_sucursal) {
            $query->whereHas('equipo', function ($q) use ($user) {
                $q->where('id_sucursal', $user->id_sucursal);
            });
        }

        if ($request->estado) {
            if ($request->estado === 'Activa') {
                $query->whereNull('fecha_devolucion');
            } else {
                $query->whereNotNull('fecha_devolucion');
            }
        }

        $asignaciones = $query->orderBy('fecha_entrega', 'desc')->get();

        $pdf = Pdf::loadView('pdf.asignaciones', compact('asignaciones'));
        return $pdf->download('reporte_asignaciones_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Reporte de empleado con sus asignaciones
     */
    public function empleado($id)
    {
        $empleado = Empleado::with(['asignaciones.equipo.marca', 'asignaciones.equipo.modelo', 'cargo', 'sucursal'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.empleado', compact('empleado'));
        return $pdf->download('reporte_empleado_' . $empleado->dni . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Reporte de reparaciones
     */
    public function reparaciones(Request $request)
    {
        $user = auth()->user();

        $query = Reparacion::with(['equipo.marca', 'equipo.modelo', 'equipo.tipoEquipo']);

        if (!$user->isAdmin() && $user->id_sucursal) {
            $query->whereHas('equipo', function ($q) use ($user) {
                $q->where('id_sucursal', $user->id_sucursal);
            });
        }

        if ($request->estado) {
            $query->where('estado_reparacion', $request->estado);
        }

        $reparaciones = $query->orderBy('fecha_ingreso', 'desc')->get();

        $pdf = Pdf::loadView('pdf.reparaciones', compact('reparaciones'));
        return $pdf->download('reporte_reparaciones_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Reporte de bajas
     */
    public function bajas(Request $request)
    {
        $user = auth()->user();

        $query = Baja::with(['equipo.marca', 'equipo.modelo', 'equipo.tipoEquipo', 'equipo.sucursal']);

        if (!$user->isAdmin() && $user->id_sucursal) {
            $query->whereHas('equipo', function ($q) use ($user) {
                $q->where('id_sucursal', $user->id_sucursal);
            });
        }

        if ($request->motivo) {
            $query->where('motivo', $request->motivo);
        }

        $bajas = $query->orderBy('fecha_baja', 'desc')->get();

        $pdf = Pdf::loadView('pdf.bajas', compact('bajas'));
        return $pdf->download('reporte_bajas_' . date('Y-m-d') . '.pdf');
    }
}
