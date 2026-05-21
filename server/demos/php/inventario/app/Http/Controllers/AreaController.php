<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Area::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
            });
        }

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $areas = $query->withCount(['empleados', 'cargos'])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('areas.index', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:areas,nombre',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Area::create($request->all());

        return redirect()->route('areas.index')
            ->with('success', 'Área creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area)
    {
        return view('areas.show', compact('area'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        return view('areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Area $area)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:areas,nombre,' . $area->id,
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $area->update($request->all());

        return redirect()->route('areas.index')
            ->with('success', 'Área actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        // Check if area has cargos or employees before deleting?
        // For now, standard delete or maybe just rely on toggle.
        // But since we implement soft delete via toggle, we might not use this route often.
        $area->delete();

        return redirect()->route('areas.index')
            ->with('success', 'Área eliminada exitosamente.');
    }

    /**
     * Toggle the status of the area.
     */
    public function toggleStatus(Area $area)
    {
        $area->estado = $area->estado === 'Activo' ? 'Inactivo' : 'Activo';
        $area->save();

        return redirect()->route('areas.index')
            ->with('success', 'El estado del área ha sido actualizado exitosamente.');
    }

    public function getCargos(Area $area)
    {
        return response()->json($area->cargos()->where('estado', 'Activo')->get());
    }
}
