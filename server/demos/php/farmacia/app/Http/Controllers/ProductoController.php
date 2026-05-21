<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $productos = Producto::with(['categoria', 'proveedor'])
            ->when($q, fn($qry) => $qry->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%$q%")
                  ->orWhere('codigo', 'like', "%$q%")
                  ->orWhere('codigo_atc', 'like', "%$q%")
                  ->orWhere('principio_activo', 'like', "%$q%");
            }))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('productos.index', compact('productos', 'q'));
    }

    public function create()
    {
        return view('productos.create', [
            'producto'    => new Producto(),
            'categorias'  => Categoria::orderBy('nombre')->get(),
            'proveedores' => Proveedor::orderBy('razon_social')->get(),
            'sucursales'  => Sucursal::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $producto = Producto::create($data);
        
        $sucursales = $request->get('sucursales', []);
        foreach ($sucursales as $sucId => $inv) {
            $producto->sucursales()->attach($sucId, [
                'stock' => $inv['stock'] ?? 0,
                'stock_minimo' => $inv['stock_minimo'] ?? 5,
                'ubicacion' => $inv['ubicacion'] ?? null,
            ]);
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', [
            'producto'    => $producto,
            'categorias'  => Categoria::orderBy('nombre')->get(),
            'proveedores' => Proveedor::orderBy('razon_social')->get(),
            'sucursales'  => Sucursal::with(['productos' => fn($q) => $q->where('producto_id', $producto->id)])->get(),
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $this->validateData($request, $producto->id);
        $producto->update($data);

        $sucursales = $request->get('sucursales', []);
        foreach ($sucursales as $sucId => $inv) {
            $producto->sucursales()->syncWithoutDetaching([
                $sucId => [
                    'stock' => $inv['stock'] ?? 0,
                    'stock_minimo' => $inv['stock_minimo'] ?? 5,
                    'ubicacion' => $inv['ubicacion'] ?? null,
                ]
            ]);
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    public function barcode(Producto $producto)
    {
        return view('productos.barcode', compact('producto'));
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'codigo'           => ['required', 'string', 'max:50', "unique:productos,codigo,{$id}"],
            'nombre'           => ['required', 'string', 'max:255'],
            'codigo_atc'       => ['nullable', 'string', 'max:20'],
            'principio_activo' => ['nullable', 'string', 'max:255'],
            'presentacion'     => ['nullable', 'string', 'max:100'],
            'concentracion'    => ['nullable', 'string', 'max:100'],
            'categoria_id'     => ['nullable', 'exists:categorias,id'],
            'proveedor_id'     => ['nullable', 'exists:proveedores,id'],
            'tipo'             => ['required', 'in:generico,marca,controlado,refrigerado,cosmetico,insumo'],
            'precio_compra'    => ['required', 'numeric', 'min:0'],
            'precio_venta'     => ['required', 'numeric', 'min:0'],
            'requiere_receta'  => ['nullable', 'boolean'],
            'activo'           => ['nullable', 'boolean'],
            'sucursales'       => ['nullable', 'array'],
        ]);
    }
}
