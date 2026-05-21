<?php

namespace App\Http\Controllers;

use App\Models\TipoEquipo;
use Illuminate\Http\Request;

class TipoEquipoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoEquipo::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
            });
        }

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $tiposEquipo = $query->withCount('equipos')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('tipos-equipo.index', compact('tiposEquipo'));
    }

    public function create()
    {
        return view('tipos-equipo.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:tipos_equipo,nombre|max:100',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        TipoEquipo::create($validated);

        return redirect()->route('tipos-equipo.index')->with('success', 'Tipo de equipo creado exitosamente.');
    }

    public function show(TipoEquipo $tiposEquipo)
    {
        $tiposEquipo->loadCount('equipos');

        return view('tipos-equipo.show', compact('tiposEquipo'));
    }

    public function edit(TipoEquipo $tiposEquipo)
    {
        return view('tipos-equipo.edit', compact('tiposEquipo'));
    }

    public function update(Request $request, TipoEquipo $tiposEquipo)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100|unique:tipos_equipo,nombre,' . $tiposEquipo->id,
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $tiposEquipo->update($validated);

        return redirect()->route('tipos-equipo.index')->with('success', 'Tipo de equipo actualizado exitosamente.');
    }

    public function destroy(TipoEquipo $tiposEquipo)
    {
        $tiposEquipo->delete();

        return redirect()->route('tipos-equipo.index')->with('success', 'Tipo de equipo eliminado exitosamente.');
    }

    public function toggleStatus(TipoEquipo $tiposEquipo)
    {
        $tiposEquipo->estado = $tiposEquipo->estado === 'Activo' ? 'Inactivo' : 'Activo';
        $tiposEquipo->save();

        return redirect()->route('tipos-equipo.index')
            ->with('success', 'El estado del tipo de equipo ha sido actualizado exitosamente.');
    }
}
