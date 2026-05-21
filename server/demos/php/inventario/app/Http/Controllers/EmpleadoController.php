<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\Area;
use App\Models\Cargo;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Calcular estadísticas (respetando rol y sucursal)
        $statsQuery = Empleado::query();
        if (!$user->isAdmin()) {
            $statsQuery->where('id_sucursal', $user->id_sucursal);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'activos' => (clone $statsQuery)->where('estado', 'Activo')->count(),
            'inactivos' => (clone $statsQuery)->where('estado', 'Inactivo')->count(),
            'con_equipos' => (clone $statsQuery)->whereHas('asignaciones', function ($q) {
                $q->where('estado', 'Activo');
            })->count(),
        ];

        $empleados = Empleado::with(['sucursal', 'cargo', 'area'])
            ->when(!$user->isAdmin() && $user->id_sucursal, function ($query) use ($user) {
                $query->where('id_sucursal', $user->id_sucursal);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('dni', 'like', "%{$search}%")
                        ->orWhere('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%");
                });
            })
            ->when($request->estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('empleados.index', compact('empleados', 'stats'));
    }

    public function export()
    {
        $user = auth()->user();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\EmpleadosExport($user), 'empleados_' . date('Y-m-d') . '.xlsx');
    }

    public function create()
    {
        $sucursales = Sucursal::where('estado', 'Activo')->get();
        $areas = Area::where('estado', 'Activo')->get();
        $cargos = Cargo::where('estado', 'Activo')->get();

        return view('empleados.create', compact('sucursales', 'areas', 'cargos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_sucursal' => 'required|exists:sucursales,id',
            'dni' => 'required|unique:empleados,dni|max:20',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'id_cargo' => 'nullable|exists:cargos,id',
            'id_area' => 'nullable|exists:areas,id',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Empleado::create($validated);

        return redirect()->route('empleados.index')->with('success', 'Empleado registrado exitosamente.');
    }

    public function show(Empleado $empleado)
    {
        $empleado->load(['sucursal', 'cargo', 'area', 'asignaciones.equipo']);

        return view('empleados.show', compact('empleado'));
    }

    public function edit(Empleado $empleado)
    {
        $user = auth()->user();

        $sucursales = $user->isAdmin()
            ? Sucursal::where('estado', 'Activo')->get()
            : Sucursal::where('id', $user->id_sucursal)->where('estado', 'Activo')->get();

        $areas = Area::where('estado', 'Activo')->get();
        $cargos = Cargo::where('estado', 'Activo')->get();

        return view('empleados.edit', compact('empleado', 'sucursales', 'areas', 'cargos'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $validated = $request->validate([
            'id_sucursal' => 'required|exists:sucursales,id',
            'dni' => 'required|max:20|unique:empleados,dni,' . $empleado->id,
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'id_cargo' => 'nullable|exists:cargos,id',
            'id_area' => 'nullable|exists:areas,id',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $empleado->update($validated);

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();

        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado exitosamente.');
    }

    /**
     * Toggle empleado status between 'Activo' and 'Inactivo'
     */
    public function toggleStatus(Empleado $empleado)
    {
        if ($empleado->estado === 'Inactivo') {
            $empleado->update(['estado' => 'Activo']);
            $message = 'Empleado activado exitosamente.';
        } else {
            $empleado->update(['estado' => 'Inactivo']);
            $message = 'Empleado desactivado exitosamente.';
        }

        return redirect()->back()->with('success', $message);
    }

}
