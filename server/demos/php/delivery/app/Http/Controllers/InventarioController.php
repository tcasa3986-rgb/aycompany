<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\MovimientoStock;
use App\Services\StockService;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::with('categoria')
            ->when($request->buscar, fn($q,$b) => $q->where(fn($s) => $s->where('nombre','like',"%$b%")->orWhere('codigo','like',"%$b%")))
            ->when($request->filtro === 'bajo', fn($q) => $q->whereColumn('stock', '<=', 'stock_minimo'))
            ->when($request->filtro === 'agotado', fn($q) => $q->where('stock', 0))
            ->orderBy('nombre')
            ->paginate(25)
            ->withQueryString();

        $resumen = [
            'total_productos' => Producto::count(),
            'stock_bajo'      => Producto::whereColumn('stock', '<=', 'stock_minimo')->where('stock', '>', 0)->count(),
            'agotados'        => Producto::where('stock', 0)->count(),
            'valor_inventario'=> Producto::selectRaw('SUM(stock * precio) as total')->value('total') ?? 0,
        ];

        return view('inventario.index', compact('productos', 'resumen'));
    }

    public function kardex(Producto $producto)
    {
        $movimientos = MovimientoStock::with('usuario','pedido')
            ->where('producto_id', $producto->id)
            ->latest()
            ->paginate(30);

        return view('inventario.kardex', compact('producto', 'movimientos'));
    }

    public function ajustar(Request $request, Producto $producto)
    {
        $request->validate([
            'tipo'     => 'required|in:entrada,ajuste,merma',
            'cantidad' => 'required|integer|not_in:0',
            'motivo'   => 'required|string|max:200',
            'costo_unitario' => 'nullable|numeric|min:0',
        ]);

        StockService::registrar($producto, $request->tipo, abs($request->cantidad), [
            'motivo'         => $request->motivo,
            'costo_unitario' => $request->costo_unitario,
        ]);

        return back()->with('success', 'Stock ajustado correctamente.');
    }
}
