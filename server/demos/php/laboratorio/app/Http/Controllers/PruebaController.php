<?php

namespace App\Http\Controllers;

use App\Models\Prueba;
use App\Models\AreaLaboratorio;
use Illuminate\Http\Request;

class PruebaController extends Controller
{
    public function index(Request $request)
    {
        $query = Prueba::with('area');
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('codigo', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%");
        }

        $pruebas = $query->orderBy('area_id')->paginate(15);
        return view('pruebas.index', compact('pruebas'));
    }

    public function create()
    {
        $areas = AreaLaboratorio::all();
        return view('pruebas.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|unique:pruebas,codigo|max:20',
            'nombre' => 'required|string|max:150',
            'area_id' => 'required|exists:areas_laboratorio,id',
            'descripcion' => 'nullable|string',
            'muestra_tipo' => 'required|string|max:80',
            'tiempo_resultado' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'unidad' => 'nullable|string|max:30',
            'valores_referencia' => 'nullable|string|max:250',
        ]);

        $validated['activo'] = $request->has('activo');

        Prueba::create($validated);

        return redirect()->route('pruebas.index')->with('success', 'Prueba registrada al catálogo correctamente.');
    }

    public function edit(Prueba $prueba)
    {
        $areas = AreaLaboratorio::all();
        return view('pruebas.edit', compact('prueba', 'areas'));
    }

    public function update(Request $request, Prueba $prueba)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:pruebas,codigo,' . $prueba->id,
            'nombre' => 'required|string|max:150',
            'area_id' => 'required|exists:areas_laboratorio,id',
            'descripcion' => 'nullable|string',
            'muestra_tipo' => 'required|string|max:80',
            'tiempo_resultado' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'unidad' => 'nullable|string|max:30',
            'valores_referencia' => 'nullable|string|max:250',
        ]);

        $validated['activo'] = $request->has('activo');

        $prueba->update($validated);

        return redirect()->route('pruebas.index')->with('success', 'Catálogo de prueba actualizado de forma correcta.');
    }

    public function destroy(Prueba $prueba)
    {
        if ($prueba->ordenDetalles()->exists()) {
            // Inactivar en lugar de eliminar si tiene registros asociados
            $prueba->update(['activo' => false]);
            return redirect()->route('pruebas.index')->with('success', 'Prueba desactivada correctamente (tiene órdenes asociadas).');
        }

        $prueba->delete();
        return redirect()->route('pruebas.index')->with('success', 'Prueba eliminada del catálogo.');
    }
}
