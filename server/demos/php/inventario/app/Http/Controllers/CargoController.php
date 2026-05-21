<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Area;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cargo::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
            });
        }

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $cargos = $query->with('area')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('cargos.index', compact('cargos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $areas = Area::where('estado', 'Activo')->get();
        return view('cargos.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:cargos,nombre',
            'id_area' => 'required|exists:areas,id',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Cargo::create($request->all());

        return redirect()->route('cargos.index')
            ->with('success', 'Cargo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cargo $cargo)
    {
        $cargo->load('area');
        return view('cargos.show', compact('cargo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cargo $cargo)
    {
        $areas = Area::where('estado', 'Activo')->get();
        return view('cargos.edit', compact('cargo', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cargo $cargo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:cargos,nombre,' . $cargo->id,
            'id_area' => 'required|exists:areas,id',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $cargo->update($request->all());

        return redirect()->route('cargos.index')
            ->with('success', 'Cargo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cargo $cargo)
    {
        $cargo->delete();

        return redirect()->route('cargos.index')
            ->with('success', 'Cargo eliminado exitosamente.');
    }

    /**
     * Toggle the status of the cargo.
     */
    public function toggleStatus(Cargo $cargo)
    {
        $cargo->estado = $cargo->estado === 'Activo' ? 'Inactivo' : 'Activo';
        $cargo->save();

        return redirect()->route('cargos.index')
            ->with('success', 'El estado del cargo ha sido actualizado exitosamente.');
    }
}
