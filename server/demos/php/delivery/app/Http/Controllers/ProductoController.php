<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('nombre', 'like', "%{$b}%")
                  ->orWhere('codigo', 'like', "%{$b}%");
            });
        }
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        if ($request->filled('disponible')) {
            $query->where('disponible', $request->disponible === '1');
        }

        $productos  = $query->latest()->paginate(20)->withQueryString();
        $categorias = Categoria::activas()->get();

        return view('productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::activas()->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_id'    => 'required|exists:categorias,id',
            'codigo'          => 'required|string|max:30|unique:productos,codigo',
            'nombre'          => 'required|string|max:150',
            'descripcion'     => 'nullable|string',
            'precio'          => 'required|numeric|min:0',
            'precio_delivery' => 'nullable|numeric|min:0',
            'unidad'          => 'required|string|max:30',
            'stock'           => 'required|integer|min:0',
            'disponible'      => 'boolean',
            'imagen'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }
        $data['disponible'] = $request->boolean('disponible');
        $data['activo']     = true;

        $producto = Producto::create($data);

        return redirect()->route('productos.index')
            ->with('success', "Producto '{$producto->nombre}' creado exitosamente.");
    }

    public function show(Producto $producto)
    {
        $producto->load('categoria');
        // últimos 10 pedidos donde aparece el producto
        $ventasRecientes = \App\Models\PedidoItem::with('pedido.cliente')
            ->where('producto_id', $producto->id)
            ->latest()
            ->limit(10)
            ->get();
        $totalVendido = \App\Models\PedidoItem::where('producto_id', $producto->id)->sum('cantidad');
        return view('productos.show', compact('producto', 'ventasRecientes', 'totalVendido'));
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::activas()->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'categoria_id'    => 'required|exists:categorias,id',
            'codigo'          => "required|string|max:30|unique:productos,codigo,{$producto->id}",
            'nombre'          => 'required|string|max:150',
            'descripcion'     => 'nullable|string',
            'precio'          => 'required|numeric|min:0',
            'precio_delivery' => 'nullable|numeric|min:0',
            'unidad'          => 'required|string|max:30',
            'stock'           => 'required|integer|min:0',
            'disponible'      => 'boolean',
            'activo'          => 'boolean',
            'imagen'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) Storage::disk('public')->delete($producto->imagen);
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }
        $data['disponible'] = $request->boolean('disponible');
        $data['activo']     = $request->boolean('activo');

        $producto->update($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen) Storage::disk('public')->delete($producto->imagen);
        $producto->delete();
        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado.');
    }
}
