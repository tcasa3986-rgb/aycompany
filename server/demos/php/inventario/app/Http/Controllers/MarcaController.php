<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index(Request $request)
    {
        $query = Marca::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
            });
        }

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $marcas = $query->withCount(['modelos', 'equipos'])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('marcas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:marcas,nombre|max:100',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Marca::create($validated);

        return redirect()->route('marcas.index')->with('success', 'Marca creada exitosamente.');
    }

    public function show(Marca $marca)
    {
        $marca->load('modelos')->loadCount(['modelos', 'equipos']);

        return view('marcas.show', compact('marca'));
    }

    public function edit(Marca $marca)
    {
        return view('marcas.edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100|unique:marcas,nombre,' . $marca->id,
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $marca->update($validated);

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada exitosamente.');
    }

    public function destroy(Marca $marca)
    {
        $marca->delete();

        return redirect()->route('marcas.index')->with('success', 'Marca eliminada exitosamente.');
    }

    public function toggleStatus(Marca $marca)
    {
        $marca->estado = $marca->estado === 'Activo' ? 'Inactivo' : 'Activo';
        $marca->save();

        return redirect()->route('marcas.index')
            ->with('success', 'El estado de la marca ha sido actualizado exitosamente.');
    }
}
