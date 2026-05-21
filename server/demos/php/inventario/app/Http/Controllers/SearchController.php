<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Empleado;
use App\Models\Asignacion;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        $user = auth()->user();

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [
            'equipos' => $this->searchEquipos($query, $user),
            'empleados' => $this->searchEmpleados($query, $user),
            'asignaciones' => $this->searchAsignaciones($query, $user),
        ];

        return response()->json($results);
    }

    private function searchEquipos($query, $user)
    {
        $equiposQuery = Equipo::with(['marca', 'modelo', 'tipoEquipo', 'sucursal'])
            ->where(function ($q) use ($query) {
                $q->where('codigo_inventario', 'LIKE', "%{$query}%")
                    ->orWhere('numero_serie', 'LIKE', "%{$query}%")
                    ->orWhereHas('marca', function ($mq) use ($query) {
                        $mq->where('nombre', 'LIKE', "%{$query}%");
                    })
                    ->orWhereHas('modelo', function ($mq) use ($query) {
                        $mq->where('nombre', 'LIKE', "%{$query}%");
                    });
            });

        if (!$user->isAdmin() && $user->id_sucursal) {
            $equiposQuery->where('id_sucursal', $user->id_sucursal);
        }

        return $equiposQuery->limit(5)->get()->map(function ($equipo) {
            return [
                'id' => $equipo->id,
                'titulo' => $equipo->codigo_inventario,
                'subtitulo' => ($equipo->marca->nombre ?? '') . ' ' . ($equipo->modelo->nombre ?? ''),
                'estado' => $equipo->estado,
                'sucursal' => $equipo->sucursal->nombre ?? 'N/A',
                'url' => route('equipos.show', $equipo),
            ];
        });
    }

    private function searchEmpleados($query, $user)
    {
        $empleadosQuery = Empleado::with(['cargo', 'sucursal'])
            ->where(function ($q) use ($query) {
                $q->where('dni', 'LIKE', "%{$query}%")
                    ->orWhere('nombres', 'LIKE', "%{$query}%")
                    ->orWhere('apellidos', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            });

        if (!$user->isAdmin() && $user->id_sucursal) {
            $empleadosQuery->where('id_sucursal', $user->id_sucursal);
        }

        return $empleadosQuery->limit(5)->get()->map(function ($empleado) {
            return [
                'id' => $empleado->id,
                'titulo' => $empleado->nombreCompleto(),
                'subtitulo' => $empleado->dni . ' - ' . ($empleado->cargo->nombre ?? 'Sin cargo'),
                'sucursal' => $empleado->sucursal->nombre ?? 'N/A',
                'url' => route('empleados.show', $empleado),
            ];
        });
    }

    private function searchAsignaciones($query, $user)
    {
        $asignacionesQuery = Asignacion::with(['equipo', 'empleado'])
            ->whereHas('equipo', function ($q) use ($query) {
                $q->where('codigo_inventario', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('empleado', function ($q) use ($query) {
                $q->where('nombres', 'LIKE', "%{$query}%")
                    ->orWhere('apellidos', 'LIKE', "%{$query}%")
                    ->orWhere('dni', 'LIKE', "%{$query}%");
            });

        if (!$user->isAdmin() && $user->id_sucursal) {
            $asignacionesQuery->whereHas('equipo', function ($q) use ($user) {
                $q->where('id_sucursal', $user->id_sucursal);
            });
        }

        return $asignacionesQuery->limit(5)->get()->map(function ($asignacion) {
            $estado = $asignacion->fecha_devolucion ? 'Finalizada' : 'Activa';
            return [
                'id' => $asignacion->id,
                'titulo' => 'Asignación: ' . $asignacion->equipo->codigo_inventario,
                'subtitulo' => $asignacion->empleado->nombreCompleto() . " - {$estado}",
                'fecha' => $asignacion->fecha_entrega->format('d/m/Y'),
                'url' => route('asignaciones.show', $asignacion),
            ];
        });
    }
}
