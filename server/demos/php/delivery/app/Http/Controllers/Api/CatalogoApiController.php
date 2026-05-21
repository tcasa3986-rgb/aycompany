<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Pedido;
use Illuminate\Http\Request;

class CatalogoApiController extends Controller
{
    public function productos(Request $request)
    {
        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->where('disponible', true)
            ->when($request->categoria_id, fn($q,$id) => $q->where('categoria_id', $id))
            ->when($request->buscar, fn($q,$b) => $q->where('nombre','like',"%$b%"))
            ->orderBy('nombre')
            ->get()
            ->map(fn($p) => [
                'id'       => $p->id,
                'codigo'   => $p->codigo,
                'nombre'   => $p->nombre,
                'precio'   => $p->precio,
                'precio_delivery' => $p->precio_delivery ?? $p->precio,
                'unidad'   => $p->unidad,
                'imagen'   => $p->imagen ? asset('storage/'.$p->imagen) : null,
                'stock'    => $p->stock,
                'categoria'=> $p->categoria?->nombre,
            ]);
        return response()->json(['data' => $productos]);
    }

    public function categorias()
    {
        return response()->json([
            'data' => Categoria::activas()->orderBy('orden')->get(['id','nombre','icono','color']),
        ]);
    }

    public function pedidos(Request $request)
    {
        $pedidos = Pedido::with('cliente')
            ->when(!$request->user()->hasAnyRole(['admin','super-admin']),
                fn($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(20);

        return response()->json($pedidos);
    }

    public function pedido(Pedido $pedido, Request $request)
    {
        if (!$request->user()->hasAnyRole(['admin','super-admin']) && $pedido->user_id !== $request->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $pedido->load(['cliente','items','repartidor','pagos']);
        return response()->json($pedido);
    }
}
