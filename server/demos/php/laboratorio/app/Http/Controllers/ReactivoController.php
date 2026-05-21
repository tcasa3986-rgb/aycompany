<?php

namespace App\Http\Controllers;

use App\Models\Reactivo;
use App\Models\AreaLaboratorio;
use Illuminate\Http\Request;

class ReactivoController extends Controller
{
    public function index(Request $request)
    {
        $query = Reactivo::with('area');

        if ($request->search) {
            $s = $request->search;
            $query->where('nombre', 'like', "%{$s}%")
                  ->orWhere('codigo', 'like', "%{$s}%")
                  ->orWhere('marca', 'like', "%{$s}%");
        }

        if ($request->area_id) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        $reactivos         = $query->orderBy('nombre')->paginate(12);
        $areas             = AreaLaboratorio::orderBy('nombre')->get();
        $stockBajoCount    = Reactivo::where('estado', 'Stock bajo')->count();
        $sinStockCount     = Reactivo::where('estado', 'Sin stock')->count();
        $vencidosCount     = Reactivo::where('estado', 'Vencido')->count();

        return view('reactivos.index', compact('reactivos', 'areas', 'stockBajoCount', 'sinStockCount', 'vencidosCount'));
    }

    public function create()
    {
        $areas = AreaLaboratorio::where('activo', true)->orderBy('nombre')->get();
        return view('reactivos.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id'          => 'required|exists:areas_laboratorio,id',
            'codigo'           => 'required|string|max:30|unique:reactivos,codigo',
            'nombre'           => 'required|string|max:150',
            'marca'            => 'nullable|string|max:100',
            'proveedor'        => 'nullable|string|max:150',
            'unidad_medida'    => 'required|string|max:30',
            'stock_actual'     => 'required|integer|min:0',
            'stock_minimo'     => 'required|integer|min:0',
            'precio_unitario'  => 'required|numeric|min:0',
            'fecha_vencimiento'=> 'nullable|date',
            'lote'             => 'nullable|string|max:50',
        ]);

        // Calcular estado inicial
        $validated['estado'] = $this->calcularEstado(
            $validated['stock_actual'],
            $validated['stock_minimo'],
            $validated['fecha_vencimiento'] ?? null
        );
        $validated['activo'] = true;

        Reactivo::create($validated);
        return redirect()->route('reactivos.index')->with('success', 'Reactivo registrado al inventario correctamente.');
    }

    public function edit(Reactivo $reactivo)
    {
        $areas = AreaLaboratorio::where('activo', true)->orderBy('nombre')->get();
        return view('reactivos.edit', compact('reactivo', 'areas'));
    }

    public function update(Request $request, Reactivo $reactivo)
    {
        $validated = $request->validate([
            'area_id'          => 'required|exists:areas_laboratorio,id',
            'codigo'           => 'required|string|max:30|unique:reactivos,codigo,' . $reactivo->id,
            'nombre'           => 'required|string|max:150',
            'marca'            => 'nullable|string|max:100',
            'proveedor'        => 'nullable|string|max:150',
            'unidad_medida'    => 'required|string|max:30',
            'stock_actual'     => 'required|integer|min:0',
            'stock_minimo'     => 'required|integer|min:0',
            'precio_unitario'  => 'required|numeric|min:0',
            'fecha_vencimiento'=> 'nullable|date',
            'lote'             => 'nullable|string|max:50',
        ]);

        $validated['estado'] = $this->calcularEstado(
            $validated['stock_actual'],
            $validated['stock_minimo'],
            $validated['fecha_vencimiento'] ?? null
        );

        $reactivo->update($validated);
        return redirect()->route('reactivos.index')->with('success', 'Reactivo actualizado correctamente.');
    }

    public function ajustarStock(Request $request, Reactivo $reactivo)
    {
        $request->validate([
            'tipo'       => 'required|in:entrada,salida',
            'cantidad'   => 'required|integer|min:1',
            'motivo'     => 'nullable|string|max:200',
        ]);

        $nuevo = $request->tipo === 'entrada'
            ? $reactivo->stock_actual + $request->cantidad
            : $reactivo->stock_actual - $request->cantidad;

        if ($nuevo < 0) {
            return back()->with('error', 'Stock insuficiente para registrar la salida.');
        }

        $reactivo->update([
            'stock_actual' => $nuevo,
            'estado'       => $this->calcularEstado($nuevo, $reactivo->stock_minimo, $reactivo->fecha_vencimiento),
        ]);

        return back()->with('success', 'Stock ajustado correctamente.');
    }

    public function destroy(Reactivo $reactivo)
    {
        $reactivo->update(['activo' => !$reactivo->activo]);
        $estado = $reactivo->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Reactivo {$estado} correctamente.");
    }

    private function calcularEstado(int $stock, int $minimo, $vencimiento): string
    {
        if ($stock <= 0) return 'Sin stock';
        if ($vencimiento && \Carbon\Carbon::parse($vencimiento)->isPast()) return 'Vencido';
        if ($stock <= $minimo) return 'Stock bajo';
        return 'Disponible';
    }
}
