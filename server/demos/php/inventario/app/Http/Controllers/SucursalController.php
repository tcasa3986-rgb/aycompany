<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index(Request $request)
    {
        $query = Sucursal::withCount(['equipos', 'empleados']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('direccion', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $sucursales = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        return view('sucursales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:sucursales,nombre|max:150',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:50',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Sucursal::create($validated);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal creada exitosamente.');
    }

    public function show(Sucursal $sucursale)
    {
        $sucursale->loadCount(['equipos', 'empleados']);

        return view('sucursales.show', compact('sucursale'));
    }

    public function edit(Sucursal $sucursale)
    {
        return view('sucursales.edit', compact('sucursale'));
    }

    public function update(Request $request, Sucursal $sucursale)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:150|unique:sucursales,nombre,' . $sucursale->id,
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:50',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $sucursale->update($validated);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Sucursal $sucursale)
    {
        $sucursale->delete();

        return redirect()->route('sucursales.index')->with('success', 'Sucursal eliminada exitosamente.');
    }

    public function toggleStatus(Sucursal $sucursale)
    {
        $sucursale->estado = $sucursale->estado === 'Activo' ? 'Inactivo' : 'Activo';
        $sucursale->save();

        return redirect()->route('sucursales.index')
            ->with('success', 'El estado de la sucursal ha sido actualizado exitosamente.');
    }
}
