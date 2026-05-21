<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    public function index()
    {
        $zonas = Zona::orderBy('distrito')->orderBy('nombre')->get();
        return view('zonas.index', compact('zonas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:100',
            'distrito'            => 'nullable|string|max:100',
            'costo_delivery'      => 'required|numeric|min:0',
            'tiempo_estimado_min' => 'required|integer|min:1|max:240',
            'monto_minimo_pedido' => 'nullable|numeric|min:0',
            'descripcion'         => 'nullable|string',
            'activo'              => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', true);

        Zona::create($data);
        return back()->with('success', 'Zona creada correctamente.');
    }

    public function update(Request $request, Zona $zona)
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:100',
            'distrito'            => 'nullable|string|max:100',
            'costo_delivery'      => 'required|numeric|min:0',
            'tiempo_estimado_min' => 'required|integer|min:1|max:240',
            'monto_minimo_pedido' => 'nullable|numeric|min:0',
            'descripcion'         => 'nullable|string',
            'activo'              => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', false);

        $zona->update($data);
        return back()->with('success', 'Zona actualizada.');
    }

    public function destroy(Zona $zona)
    {
        if ($zona->pedidos()->exists()) {
            return back()->with('error', 'No se puede eliminar: zona con pedidos asociados.');
        }
        $zona->delete();
        return back()->with('success', 'Zona eliminada.');
    }

    /**
     * Endpoint para autocompletar costo al crear pedido.
     */
    public function tarifa(Zona $zona)
    {
        return response()->json([
            'costo_delivery'      => (float) $zona->costo_delivery,
            'tiempo_estimado_min' => (int) $zona->tiempo_estimado_min,
            'monto_minimo_pedido' => (float) $zona->monto_minimo_pedido,
            'distrito'            => $zona->distrito,
        ]);
    }
}
