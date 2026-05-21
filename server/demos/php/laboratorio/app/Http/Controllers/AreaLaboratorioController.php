<?php

namespace App\Http\Controllers;

use App\Models\AreaLaboratorio;
use Illuminate\Http\Request;

class AreaLaboratorioController extends Controller
{
    public function index()
    {
        $areas = AreaLaboratorio::withCount('pruebas')->withCount('reactivos')->latest()->paginate(10);
        return view('areas.index', compact('areas'));
    }

    public function create()
    {
        return view('areas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:100|unique:areas_laboratorio,nombre',
            'descripcion' => 'nullable|string|max:250',
            'color'       => 'nullable|string|max:7',
        ]);

        $validated['activo'] = true;
        AreaLaboratorio::create($validated);

        return redirect()->route('areas.index')->with('success', 'Área de laboratorio registrada correctamente.');
    }

    public function edit(AreaLaboratorio $area)
    {
        return view('areas.edit', compact('area'));
    }

    public function update(Request $request, AreaLaboratorio $area)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:100|unique:areas_laboratorio,nombre,' . $area->id,
            'descripcion' => 'nullable|string|max:250',
            'color'       => 'nullable|string|max:7',
        ]);

        $area->update($validated);
        return redirect()->route('areas.index')->with('success', 'Área actualizada correctamente.');
    }

    public function destroy(AreaLaboratorio $area)
    {
        if ($area->pruebas()->exists() || $area->reactivos()->exists()) {
            return back()->with('error', 'No se puede eliminar el área: tiene pruebas o reactivos asociados.');
        }

        $area->delete();
        return redirect()->route('areas.index')->with('success', 'Área eliminada correctamente.');
    }
}
