<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Models\Marca;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function index(Request $request)
    {
        $query = Modelo::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
            });
        }

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $modelos = $query->with(['marca', 'equipos'])
            ->withCount('equipos')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('modelos.index', compact('modelos'));
    }

    public function create()
    {
        $marcas = Marca::where('estado', 'Activo')->orderBy('nombre')->get();
        return view('modelos.create', compact('marcas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_marca' => 'required|exists:marcas,id',
            'nombre' => 'required|max:100', // Uniqueness check could be scoped to brand, but kept simple for now
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Modelo::create($validated);

        return redirect()->route('modelos.index')->with('success', 'Modelo creado exitosamente.');
    }

    public function show(Modelo $modelo)
    {
        $modelo->load(['marca', 'equipos']);
        return view('modelos.show', compact('modelo'));
    }

    public function edit(Modelo $modelo)
    {
        $marcas = Marca::where('estado', 'Activo')->orderBy('nombre')->get();
        return view('modelos.edit', compact('modelo', 'marcas'));
    }

    public function update(Request $request, Modelo $modelo)
    {
        $validated = $request->validate([
            'id_marca' => 'required|exists:marcas,id',
            'nombre' => 'required|max:100',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $modelo->update($validated);

        return redirect()->route('modelos.index')->with('success', 'Modelo actualizado exitosamente.');
    }

    public function destroy(Modelo $modelo)
    {
        $modelo->delete();

        return redirect()->route('modelos.index')->with('success', 'Modelo eliminado exitosamente.');
    }

    public function toggleStatus(Modelo $modelo)
    {
        $modelo->estado = $modelo->estado === 'Activo' ? 'Inactivo' : 'Activo';
        $modelo->save();

        return redirect()->route('modelos.index')
            ->with('success', 'El estado del modelo ha sido actualizado exitosamente.');
    }
}
