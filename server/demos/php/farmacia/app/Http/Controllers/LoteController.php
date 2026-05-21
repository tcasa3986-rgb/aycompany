<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoteController extends Controller
{
    public function index(Producto $producto)
    {
        $lotes = $producto->lotes()->orderBy('fecha_vencimiento')->get();
        $totalEnLotes = $lotes->sum('cantidad');
        return view('lotes.index', compact('producto', 'lotes', 'totalEnLotes'));
    }

    public function store(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'numero_lote'       => ['required', 'string', 'max:80'],
            'fecha_vencimiento' => ['required', 'date'],
            'cantidad'          => ['required', 'integer', 'min:1'],
            'sumar_stock'       => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data, $producto) {
            Lote::create([
                'producto_id'       => $producto->id,
                'numero_lote'       => $data['numero_lote'],
                'fecha_vencimiento' => $data['fecha_vencimiento'],
                'cantidad'          => $data['cantidad'],
            ]);

            if (! empty($data['sumar_stock'])) {
                $producto->increment('stock', $data['cantidad']);
            }
        });

        return redirect()->route('productos.lotes.index', $producto)
            ->with('success', 'Lote agregado.');
    }

    public function update(Request $request, Producto $producto, Lote $lote)
    {
        abort_unless($lote->producto_id === $producto->id, 404);

        $data = $request->validate([
            'numero_lote'       => ['required', 'string', 'max:80'],
            'fecha_vencimiento' => ['required', 'date'],
            'cantidad'          => ['required', 'integer', 'min:0'],
        ]);

        $lote->update($data);
        return redirect()->route('productos.lotes.index', $producto)->with('success', 'Lote actualizado.');
    }

    public function destroy(Producto $producto, Lote $lote)
    {
        abort_unless($lote->producto_id === $producto->id, 404);
        $lote->delete();
        return back()->with('success', 'Lote eliminado.');
    }
}
