<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Sucursal;
use App\Models\TipoEquipo;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Http\Request;
use App\Exports\EquiposExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class EquipoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Calcular estadísticas (respetando rol)
        $statsQuery = Equipo::query();
        if (!$user->isAdmin()) {
            $statsQuery->where('id_sucursal', $user->id_sucursal);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'disponibles' => (clone $statsQuery)->where('estado', 'Disponible')->count(),
            'en_reparacion' => (clone $statsQuery)->where('estado', 'En Reparacion')->count(),
            'asignados' => (clone $statsQuery)->where('estado', 'Asignado')->count(),
        ];

        // Query principal para el listado
        $query = Equipo::with(['sucursal', 'tipoEquipo', 'marca', 'modelo']);

        // Filtrar por sucursal si no es admin
        if (!$user->isAdmin()) {
            $query->where('id_sucursal', $user->id_sucursal);
        }

        // Búsqueda por código o número de serie
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_inventario', 'LIKE', "%{$search}%")
                    ->orWhere('numero_serie', 'LIKE', "%{$search}%");
            });
        }

        // Filtrar por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $equipos = $query->latest()->paginate(10)->withQueryString();

        return view('equipos.index', compact('equipos', 'stats'));
    }

    public function create()
    {
        $user = auth()->user();

        $sucursales = $user->isAdmin()
            ? Sucursal::where('estado', 'Activo')->get()
            : Sucursal::where('id', $user->id_sucursal)->where('estado', 'Activo')->get();

        $tiposEquipo = TipoEquipo::where('estado', 'Activo')->get();
        $marcas = Marca::where('estado', 'Activo')->get();

        return view('equipos.create', compact('sucursales', 'tiposEquipo', 'marcas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_sucursal' => 'required|exists:sucursales,id',
            'codigo_inventario' => 'required|unique:equipos,codigo_inventario|max:50',
            'id_tipo_equipo' => 'required|exists:tipos_equipo,id',
            'id_marca' => 'required|exists:marcas,id',
            'id_modelo' => 'required|exists:modelos,id',
            'numero_serie' => 'required|unique:equipos,numero_serie|max:100',
            'caracteristicas' => 'nullable|string',
            'tipo_adquisicion' => 'required|in:Propio,Arrendado,Prestamo',
            'fecha_adquisicion' => 'nullable|date',
            'costo' => 'nullable|numeric|min:0',
            'numero_guia' => 'nullable|string|max:100',
            'archivo_guia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'proveedor' => 'nullable|string|max:150',
            'estado' => 'required|in:Disponible,Asignado,En Reparacion,De Baja',
            'observaciones' => 'nullable|string',
        ]);

        if ($request->hasFile('archivo_guia')) {
            $path = $request->file('archivo_guia')->store('equipos/guias', 'public');
            $validated['archivo_guia'] = $path;
        }

        Equipo::create($validated);

        return redirect()->route('equipos.index')->with('success', 'Equipo registrado exitosamente.');
    }

    public function show(Equipo $equipo)
    {
        $equipo->load(['sucursal', 'tipoEquipo', 'marca', 'modelo', 'asignaciones.empleado']);

        return view('equipos.show', compact('equipo'));
    }

    public function edit(Equipo $equipo)
    {
        $user = auth()->user();

        $sucursales = $user->isAdmin()
            ? Sucursal::where('estado', 'Activo')->get()
            : Sucursal::where('id', $user->id_sucursal)->where('estado', 'Activo')->get();

        $tiposEquipo = TipoEquipo::where('estado', 'Activo')->get();
        $marcas = Marca::where('estado', 'Activo')->get();
        $modelos = Modelo::where('id_marca', $equipo->id_marca)->where('estado', 'Activo')->get();

        return view('equipos.edit', compact('equipo', 'sucursales', 'tiposEquipo', 'marcas', 'modelos'));
    }

    public function update(Request $request, Equipo $equipo)
    {
        $validated = $request->validate([
            'id_sucursal' => 'required|exists:sucursales,id',
            'codigo_inventario' => 'required|max:50|unique:equipos,codigo_inventario,' . $equipo->id,
            'id_tipo_equipo' => 'required|exists:tipos_equipo,id',
            'id_marca' => 'required|exists:marcas,id',
            'id_modelo' => 'required|exists:modelos,id',
            'numero_serie' => 'required|max:100|unique:equipos,numero_serie,' . $equipo->id,
            'caracteristicas' => 'nullable|string',
            'tipo_adquisicion' => 'required|in:Propio,Arrendado,Prestamo',
            'fecha_adquisicion' => 'nullable|date',
            'costo' => 'nullable|numeric|min:0',
            'numero_guia' => 'nullable|string|max:100',
            'archivo_guia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'proveedor' => 'nullable|string|max:150',
            'estado' => 'required|in:Disponible,Asignado,En Reparacion,De Baja',
            'observaciones' => 'nullable|string',
        ]);

        if ($request->hasFile('archivo_guia')) {
            // Eliminar archivo anterior si existe
            if ($equipo->archivo_guia) {
                Storage::disk('public')->delete($equipo->archivo_guia);
            }
            $path = $request->file('archivo_guia')->store('equipos/guias', 'public');
            $validated['archivo_guia'] = $path;
        }

        $equipo->update($validated);

        return redirect()->route('equipos.index')->with('success', 'Equipo actualizado exitosamente.');
    }

    public function destroy(Equipo $equipo)
    {
        $equipo->delete();

        return redirect()->route('equipos.index')->with('success', 'Equipo eliminado exitosamente.');
    }

    /**
     * Toggle equipo status between 'De Baja' and 'Disponible'
     */
    public function toggleStatus(Equipo $equipo)
    {
        if ($equipo->estado === 'De Baja') {
            $equipo->update(['estado' => 'Disponible']);
            $message = 'Equipo activado exitosamente (Estado: Disponible).';
        } else {
            // Si está asignado, advertimos o permitimos? 
            // Por ahora permitimos desactivar, asumiendo que el usuario sabe lo que hace.
            // Idealmente deberíamos validar si tiene asignaciones activas.
            $equipo->update(['estado' => 'De Baja']);
            $message = 'Equipo desactivado exitosamente (Estado: De Baja).';
        }

        return redirect()->back()->with('success', $message);
    }


    /**
     * Export equipos to Excel
     */
    public function export()
    {
        $user = auth()->user();
        return Excel::download(new EquiposExport($user), 'equipos_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Get modelos by marca (AJAX endpoint)
     */
    public function getModelos($marcaId)
    {
        $modelos = Modelo::where('id_marca', $marcaId)->where('estado', 'Activo')->get();
        return response()->json($modelos);
    }
}
