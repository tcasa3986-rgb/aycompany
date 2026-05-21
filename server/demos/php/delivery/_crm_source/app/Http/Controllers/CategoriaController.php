<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('productos')->orderBy('orden')->get();
        return view('categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:80',
            'descripcion' => 'nullable|string',
            'icono'       => 'nullable|string|max:50',
            'color'       => 'nullable|string|max:20',
            'orden'       => 'nullable|integer',
        ]);
        $data['slug']   = Str::slug($data['nombre']);
        $data['activo'] = true;

        Categoria::create($data);

        return back()->with('success', 'Categoría creada exitosamente.');
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:80',
            'descripcion' => 'nullable|string',
            'icono'       => 'nullable|string|max:50',
            'color'       => 'nullable|string|max:20',
            'orden'       => 'nullable|integer',
            'activo'      => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo');
        $categoria->update($data);

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->exists()) {
            return back()->with('error', 'No se puede eliminar: la categoría tiene productos asociados.');
        }
        $categoria->delete();
        return back()->with('success', 'Categoría eliminada.');
    }
}
