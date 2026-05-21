<?php

namespace App\Http\Controllers;

use App\Models\Reparacion;
use App\Models\Equipo;
use Illuminate\Http\Request;

class ReparacionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $reparaciones = Reparacion::with(['equipo.marca', 'equipo.modelo', 'equipo.tipoEquipo'])
            ->when(!$user->isAdmin() && $user->id_sucursal, function ($query) use ($user) {
                $query->whereHas('equipo', function ($q) use ($user) {
                    $q->where('id_sucursal', $user->id_sucursal);
                });
            })
            ->when($request->estado, function ($query, $estado) {
                $query->where('estado_reparacion', $estado);
            })
            ->when($request->search, function ($query, $search) {
                $query->whereHas('equipo', function ($q) use ($search) {
                    $q->where('codigo_inventario', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('reparaciones.index', compact('reparaciones'));
    }

    public function create()
    {
        $user = auth()->user();

        // Solo equipos disponibles o asignados (no en reparación ni de baja)
        $equipos = Equipo::with(['marca', 'modelo', 'tipoEquipo'])
            ->whereIn('estado', ['Disponible', 'Asignado'])
            ->when(!$user->isAdmin() && $user->id_sucursal, function ($query) use ($user) {
                $query->where('id_sucursal', $user->id_sucursal);
            })
            ->get();

        return view('reparaciones.create', compact('equipos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_equipo' => 'required|exists:equipos,id',
            'fecha_ingreso' => 'required|date',
            'descripcion_problema' => 'required|string',
            'tecnico_asignado' => 'nullable|string|max:100',
            'costo_estimado' => 'nullable|numeric|min:0',
        ]);

        $validated['estado_reparacion'] = 'Pendiente';
        $reparacion = Reparacion::create($validated);

        // Actualizar estado del equipo
        $equipo = Equipo::find($validated['id_equipo']);
        $equipo->update(['estado' => 'En Reparacion']);

        return redirect()->route('reparaciones.index')->with('success', 'Reparación registrada exitosamente.');
    }

    public function show(Reparacion $reparacione)
    {
        $reparacione->load(['equipo.marca', 'equipo.modelo', 'equipo.tipoEquipo', 'equipo.sucursal']);
        return view('reparaciones.show', compact('reparacione'));
    }

    public function edit(Reparacion $reparacione)
    {
        return view('reparaciones.edit', compact('reparacione'));
    }

    public function update(Request $request, Reparacion $reparacione)
    {
        $validated = $request->validate([
            'fecha_salida' => 'nullable|date',
            'estado_reparacion' => 'required|in:Pendiente,En Proceso,Completada,Cancelada',
            'descripcion_solucion' => 'nullable|string',
            'tecnico_asignado' => 'nullable|string|max:100',
            'costo_real' => 'nullable|numeric|min:0',
        ]);

        $estadoAnterior = $reparacione->estado_reparacion;
        $reparacione->update($validated);

        // Si se completa o cancela la reparación, cambiar estado del equipo
        if (in_array($validated['estado_reparacion'], ['Completada', 'Cancelada']) && $estadoAnterior !== $validated['estado_reparacion']) {
            $reparacione->equipo->update(['estado' => 'Disponible']);
        }

        // Si pasa a En Proceso, asegurar que el equipo esté en reparación
        if ($validated['estado_reparacion'] === 'En Proceso') {
            $reparacione->equipo->update(['estado' => 'En Reparacion']);
        }

        return redirect()->route('reparaciones.index')->with('success', 'Reparación actualizada exitosamente.');
    }

    public function destroy(Reparacion $reparacione)
    {
        // Si está en proceso, liberar el equipo
        if (in_array($reparacione->estado_reparacion, ['Pendiente', 'En Proceso'])) {
            $reparacione->equipo->update(['estado' => 'Disponible']);
        }

        $reparacione->delete();

        return redirect()->route('reparaciones.index')->with('success', 'Reparación eliminada exitosamente.');
    }
}
