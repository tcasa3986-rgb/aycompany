<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->get('estado');
        $compras = Compra::with(['proveedor'])
            ->when($estado, fn($q) => $q->where('estado', $estado))
            ->orderByDesc('fecha')
            ->paginate(15)
            ->withQueryString();
        return view('compras.index', compact('compras', 'estado'));
    }

    public function create()
    {
        return view('compras.create', [
            'proveedores' => Proveedor::where('activo', true)->orderBy('razon_social')->get(),
            'productos'   => Producto::where('activo', true)->orderBy('nombre')->get(['id', 'codigo', 'nombre', 'precio_compra']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proveedor_id'   => ['required', 'exists:proveedores,id'],
            'observaciones'  => ['nullable', 'string', 'max:500'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.producto_id'       => ['required', 'exists:productos,id'],
            'items.*.cantidad'          => ['required', 'integer', 'min:1'],
            'items.*.precio_unitario'   => ['required', 'numeric', 'min:0'],
            'items.*.numero_lote'       => ['nullable', 'string', 'max:80'],
            'items.*.fecha_vencimiento' => ['nullable', 'date'],
        ]);

        return DB::transaction(function () use ($data) {
            $subtotal = 0;
            foreach ($data['items'] as $it) {
                $subtotal += $it['cantidad'] * $it['precio_unitario'];
            }
            $impuesto = round($subtotal * 0.18, 2);
            $total    = $subtotal + $impuesto;

            $compra = Compra::create([
                'codigo'        => 'OC-' . now()->format('YmdHis'),
                'proveedor_id'  => $data['proveedor_id'],
                'user_id'       => Auth::id(),
                'estado'        => 'pendiente',
                'subtotal'      => $subtotal,
                'impuesto'      => $impuesto,
                'total'         => $total,
                'fecha'         => now(),
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            foreach ($data['items'] as $it) {
                DetalleCompra::create([
                    'compra_id'         => $compra->id,
                    'producto_id'       => $it['producto_id'],
                    'numero_lote'       => $it['numero_lote'] ?? null,
                    'fecha_vencimiento' => $it['fecha_vencimiento'] ?? null,
                    'cantidad'          => $it['cantidad'],
                    'precio_unitario'   => $it['precio_unitario'],
                    'subtotal'          => $it['cantidad'] * $it['precio_unitario'],
                ]);
            }

            return redirect()->route('compras.show', $compra)
                ->with('success', "Orden de compra {$compra->codigo} creada.");
        });
    }

    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'detalles.producto']);
        return view('compras.show', compact('compra'));
    }

    public function recibir(Compra $compra)
    {
        if ($compra->estado === 'recibida' || $compra->estado === 'anulada') {
            return back()->with('error', 'Esta compra ya no se puede recibir.');
        }

        return DB::transaction(function () use ($compra) {
            $compra->load('detalles');

            foreach ($compra->detalles as $det) {
                Producto::where('id', $det->producto_id)->increment('stock', $det->cantidad);

                if ($det->numero_lote && $det->fecha_vencimiento) {
                    Lote::updateOrCreate(
                        ['producto_id' => $det->producto_id, 'numero_lote' => $det->numero_lote],
                        ['fecha_vencimiento' => $det->fecha_vencimiento, 'cantidad' => $det->cantidad]
                    );
                }
            }

            $compra->update([
                'estado'          => 'recibida',
                'fecha_recepcion' => now()->toDateString(),
            ]);

            return redirect()->route('compras.show', $compra)
                ->with('success', 'Mercadería recibida y stock actualizado.');
        });
    }

    public function anular(Compra $compra)
    {
        if ($compra->estado === 'recibida') {
            return back()->with('error', 'No se puede anular una compra ya recibida.');
        }
        $compra->update(['estado' => 'anulada']);
        return back()->with('success', 'Compra anulada.');
    }
}
