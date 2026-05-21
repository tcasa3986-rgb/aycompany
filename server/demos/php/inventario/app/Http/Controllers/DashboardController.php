<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Empleado;
use App\Models\Asignacion;
use App\Models\Reparacion;
use App\Models\Baja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $sucursalId = $request->get('sucursal_id');

        // Si el usuario no es admin, forzar su sucursal de todas formas
        if (!$user->isAdmin() && $user->id_sucursal) {
            $sucursalId = $user->id_sucursal;
        }

        // Obtener todas las sucursales para el filtro (solo si es admin)
        $sucursales = [];
        if ($user->isAdmin()) {
            $sucursales = \App\Models\Sucursal::where('estado', 'Activo')->get();
        }

        // Estadísticas generales
        $stats = [
            'total_equipos' => $this->getEquiposCount($user, $sucursalId),
            'equipos_disponibles' => $this->getEquiposDisponibles($user, $sucursalId),
            'equipos_asignados' => $this->getEquiposAsignados($user, $sucursalId),
            'equipos_reparacion' => $this->getEquiposReparacion($user, $sucursalId),
            'total_empleados' => $this->getEmpleadosCount($user, $sucursalId),
            'total_asignaciones_activas' => $this->getAsignacionesActivas($user, $sucursalId),
            'total_reparaciones_pendientes' => $this->getReparacionesPendientes($user, $sucursalId),
            'total_bajas' => $this->getBajasCount($user, $sucursalId),
        ];

        // Distribución de equipos por estado
        $equiposPorEstado = $this->getEquiposPorEstado($user, $sucursalId);

        // Distribución de equipos por tipo
        $equiposPorTipo = $this->getEquiposPorTipo($user, $sucursalId);

        // Top 5 empleados con más equipos asignados
        $topEmpleados = $this->getTopEmpleadosConEquipos($user, $sucursalId);

        // Reparaciones recientes (últimas 5)
        $reparacionesRecientes = $this->getReparacionesRecientes($user, $sucursalId);

        // Costos de reparación del mes actual
        $costosReparacion = $this->getCostosReparacionMesActual($user, $sucursalId);

        // Bajas por motivo
        $bajasPorMotivo = $this->getBajasPorMotivo($user, $sucursalId);

        // Actividad reciente
        $actividadReciente = $this->getActividadReciente($user, $sucursalId);

        // Alertas importantes
        $alertas = $this->getAlertas($user, $sucursalId);

        // Tendencia mensual de asignaciones
        $tendenciaMensual = $this->getTendenciaMensual($user, $sucursalId);

        return view('dashboard', compact(
            'stats',
            'equiposPorEstado',
            'equiposPorTipo',
            'topEmpleados',
            'reparacionesRecientes',
            'costosReparacion',
            'bajasPorMotivo',
            'actividadReciente',
            'alertas',
            'tendenciaMensual',
            'sucursales',
            'sucursalId'
        ));
    }

    private function applyFilter($query, $user, $sucursalId, $field = 'id_sucursal', $relation = null)
    {
        if (!$user->isAdmin() && $user->id_sucursal) {
            // Usuario normal: filtrar por su sucursal
            if ($relation) {
                $query->whereHas($relation, function ($q) use ($user, $field) {
                    $q->where($field, $user->id_sucursal);
                });
            } else {
                $query->where($field, $user->id_sucursal);
            }
        } elseif ($sucursalId) {
            // Admin con filtro seleccionado
            if ($relation) {
                $query->whereHas($relation, function ($q) use ($sucursalId, $field) {
                    $q->where($field, $sucursalId);
                });
            } else {
                $query->where($field, $sucursalId);
            }
        }
        return $query;
    }

    private function getEquiposCount($user, $sucursalId)
    {
        $query = Equipo::query();
        $this->applyFilter($query, $user, $sucursalId);
        return $query->count();
    }

    private function getEquiposDisponibles($user, $sucursalId)
    {
        $query = Equipo::where('estado', 'Disponible');
        $this->applyFilter($query, $user, $sucursalId);
        return $query->count();
    }

    private function getEquiposAsignados($user, $sucursalId)
    {
        $query = Equipo::where('estado', 'Asignado');
        $this->applyFilter($query, $user, $sucursalId);
        return $query->count();
    }

    private function getEquiposReparacion($user, $sucursalId)
    {
        $query = Equipo::where('estado', 'En Reparacion');
        $this->applyFilter($query, $user, $sucursalId);
        return $query->count();
    }

    private function getEmpleadosCount($user, $sucursalId)
    {
        $query = Empleado::query();
        $this->applyFilter($query, $user, $sucursalId);
        return $query->count();
    }

    private function getAsignacionesActivas($user, $sucursalId)
    {
        $query = Asignacion::whereNull('fecha_devolucion');
        $this->applyFilter($query, $user, $sucursalId, 'id_sucursal', 'equipo');
        return $query->count();
    }

    private function getReparacionesPendientes($user, $sucursalId)
    {
        $query = Reparacion::whereIn('estado_reparacion', ['Pendiente', 'En Proceso']);
        $this->applyFilter($query, $user, $sucursalId, 'id_sucursal', 'equipo');
        return $query->count();
    }

    private function getBajasCount($user, $sucursalId)
    {
        $query = Baja::query();
        $this->applyFilter($query, $user, $sucursalId, 'id_sucursal', 'equipo');
        return $query->count();
    }

    private function getEquiposPorEstado($user, $sucursalId)
    {
        $query = Equipo::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado');
        $this->applyFilter($query, $user, $sucursalId);
        return $query->get();
    }

    private function getEquiposPorTipo($user, $sucursalId)
    {
        $query = Equipo::select('tipos_equipo.nombre as tipo', DB::raw('count(*) as total'))
            ->join('tipos_equipo', 'equipos.id_tipo_equipo', '=', 'tipos_equipo.id')
            ->groupBy('tipos_equipo.nombre');

        // Apply filter manually because of join
        if (!$user->isAdmin() && $user->id_sucursal) {
            $query->where('equipos.id_sucursal', $user->id_sucursal);
        } elseif ($sucursalId) {
            $query->where('equipos.id_sucursal', $sucursalId);
        }

        return $query->get();
    }

    private function getTopEmpleadosConEquipos($user, $sucursalId)
    {
        $query = Empleado::select('empleados.*', DB::raw('COUNT(asignaciones.id) as equipos_asignados'))
            ->leftJoin('asignaciones', function ($join) {
                $join->on('empleados.id', '=', 'asignaciones.id_empleado')
                    ->whereNull('asignaciones.fecha_devolucion');
            })
            ->groupBy('empleados.id')
            ->orderBy('equipos_asignados', 'desc')
            ->limit(5);

        $this->applyFilter($query, $user, $sucursalId);

        return $query->get();
    }

    private function getReparacionesRecientes($user, $sucursalId)
    {
        $query = Reparacion::with(['equipo.marca', 'equipo.modelo'])
            ->orderBy('created_at', 'desc')
            ->limit(5);
        $this->applyFilter($query, $user, $sucursalId, 'id_sucursal', 'equipo');
        return $query->get();
    }

    private function getCostosReparacionMesActual($user, $sucursalId)
    {
        $query = Reparacion::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'));
        $this->applyFilter($query, $user, $sucursalId, 'id_sucursal', 'equipo');

        $total = $query->sum('costo_real') ?? 0;

        return [
            'total' => $total,
            'count' => $query->count(),
        ];
    }

    private function getBajasPorMotivo($user, $sucursalId)
    {
        $query = Baja::select('motivo', DB::raw('count(*) as total'))
            ->groupBy('motivo');
        $this->applyFilter($query, $user, $sucursalId, 'id_sucursal', 'equipo');
        return $query->get();
    }

    private function getActividadReciente($user, $sucursalId)
    {
        $actividades = collect();

        // Últimas asignaciones
        $asignacionesQuery = Asignacion::with(['equipo', 'empleado'])
            ->orderBy('created_at', 'desc')
            ->limit(3);
        $this->applyFilter($asignacionesQuery, $user, $sucursalId, 'id_sucursal', 'equipo');
        $asignaciones = $asignacionesQuery->get()->map(function ($item) {
            return [
                'tipo' => 'asignacion',
                'descripcion' => "Equipo {$item->equipo->codigo_inventario} asignado a {$item->empleado->nombreCompleto()}",
                'fecha' => $item->created_at,
                'icono' => 'clipboard-check',
                'color' => 'blue',
            ];
        });

        // Últimas reparaciones
        $reparacionesQuery = Reparacion::with(['equipo'])
            ->orderBy('created_at', 'desc')
            ->limit(3);
        $this->applyFilter($reparacionesQuery, $user, $sucursalId, 'id_sucursal', 'equipo');
        $reparaciones = $reparacionesQuery->get()->map(function ($item) {
            return [
                'tipo' => 'reparacion',
                'descripcion' => "Reparación registrada para equipo {$item->equipo->codigo_inventario}",
                'fecha' => $item->created_at,
                'icono' => 'wrench',
                'color' => 'orange',
            ];
        });

        return $actividades->concat($asignaciones)->concat($reparaciones)
            ->sortByDesc('fecha')
            ->take(6)
            ->values();
    }

    private function getAlertas($user, $sucursalId)
    {
        $alertas = [];

        // Reparaciones pendientes hace más de 30 días
        $reparacionesAtrasadasQuery = Reparacion::whereIn('estado_reparacion', ['Pendiente', 'En Proceso'])
            ->where('fecha_ingreso', '<', now()->subDays(30));
        $this->applyFilter($reparacionesAtrasadasQuery, $user, $sucursalId, 'id_sucursal', 'equipo');
        $reparacionesAtrasadas = $reparacionesAtrasadasQuery->count();

        if ($reparacionesAtrasadas > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'mensaje' => "{$reparacionesAtrasadas} reparaciones pendientes desde hace más de 30 días",
                'accion' => route('reparaciones.index'),
            ];
        }

        // Empleados con más de 3 equipos asignados
        $empleadosSobrecargadosQuery = Empleado::select('empleados.*', DB::raw('COUNT(asignaciones.id) as total'))
            ->leftJoin('asignaciones', function ($join) {
                $join->on('empleados.id', '=', 'asignaciones.id_empleado')
                    ->whereNull('asignaciones.fecha_devolucion');
            })
            ->groupBy('empleados.id')
            ->having('total', '>', 3);

        $this->applyFilter($empleadosSobrecargadosQuery, $user, $sucursalId);
        $empleadosSobrecargados = $empleadosSobrecargadosQuery->get()->count(); // count() on builder with having might differ, get()->count() is safer for simple display

        if ($empleadosSobrecargados > 0) {
            $alertas[] = [
                'tipo' => 'info',
                'mensaje' => "{$empleadosSobrecargados} empleados tienen más de 3 equipos asignados",
                'accion' => route('empleados.index'),
            ];
        }

        return $alertas;
    }

    private function getTendenciaMensual($user, $sucursalId)
    {
        $meses = [];
        $datos = [];

        // Generar últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $meses[] = $fecha->locale('es')->translatedFormat('M'); // Ene, Feb, Mar...

            $query = Asignacion::whereMonth('created_at', $fecha->month)
                ->whereYear('created_at', $fecha->year);

            $this->applyFilter($query, $user, $sucursalId, 'id_sucursal', 'equipo');

            $datos[] = $query->count();
        }

        return [
            'meses' => $meses,
            'datos' => $datos,
        ];
    }
}

