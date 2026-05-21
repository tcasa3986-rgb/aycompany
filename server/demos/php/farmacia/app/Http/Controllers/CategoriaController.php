<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $categorias = Categoria::query()
            ->withCount('productos')
            ->when($q, fn($qry) => $qry->where('nombre', 'like', "%$q%"))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();
        return view('categorias.index', compact('categorias', 'q'));
    }

    public function create()
    {
        return view('categorias.create', ['categoria' => new Categoria()]);
    }

    public function store(Request $request)
    {
        Categoria::create($this->validateData($request));
        return redirect()->route('categorias.index')->with('success', 'Categoría creada.');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $categoria->update($this->validateData($request, $categoria->id));
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada.');
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre'      => ['required', 'string', 'max:120', "unique:categorias,nombre,{$id}"],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo'      => ['nullable', 'boolean'],
        ]);
    }
}
