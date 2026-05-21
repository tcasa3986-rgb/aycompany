<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetalleReceta;
use App\Models\Producto;
use App\Models\Receta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $recetas = Receta::with(['cliente', 'farmaceutico'])
            ->when($q, fn($qry) => $qry->where(function ($w) use ($q) {
                $w->where('codigo', 'like', "%$q%")
                  ->orWhere('medico', 'like', "%$q%")
                  ->orWhereHas('cliente', fn($c) => $c->where('nombres', 'like', "%$q%")
                                                       ->orWhere('apellidos', 'like', "%$q%")
                                                       ->orWhere('documento', 'like', "%$q%"));
            }))
            ->orderByDesc('fecha')
            ->paginate(15)
            ->withQueryString();

        return view('recetas.index', compact('recetas', 'q'));
    }

    public function create()
    {
        return view('recetas.create', [
            'clientes'  => Cliente::orderBy('nombres')->get(['id', 'documento', 'nombres', 'apellidos']),
            'productos' => Producto::where('activo', true)->orderBy('nombre')
                              ->get(['id', 'codigo', 'nombre', 'concentracion', 'requiere_receta']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'    => ['nullable', 'exists:clientes,id'],
            'medico'        => ['required', 'string', 'max:200'],
            'especialidad'  => ['nullable', 'string', 'max:120'],
            'cmp'           => ['nullable', 'string', 'max:20'],
            'fecha'         => ['required', 'date'],
            'retenida'      => ['nullable', 'boolean'],
            'diagnostico'   => ['nullable', 'string', 'max:500'],
            'observaciones' => ['nullable', 'string', 'max:500'],
            'items'         => ['required', 'array', 'min:1'],
            'items.*.producto_id'  => ['required', 'exists:productos,id'],
            'items.*.cantidad'     => ['required', 'integer', 'min:1'],
            'items.*.indicaciones' => ['nullable', 'string', 'max:255'],
        ]);

        return DB::transaction(function () use ($data) {
            $receta = Receta::create([
                'codigo'        => 'RX-' . now()->format('YmdHis'),
                'cliente_id'    => $data['cliente_id'] ?? null,
                'user_id'       => Auth::id(),
                'medico'        => $data['medico'],
                'especialidad'  => $data['especialidad'] ?? null,
                'cmp'           => $data['cmp'] ?? null,
                'fecha'         => $data['fecha'],
                'retenida'      => (bool) ($data['retenida'] ?? false),
                'diagnostico'   => $data['diagnostico'] ?? null,
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                DetalleReceta::create([
                    'receta_id'    => $receta->id,
                    'producto_id'  => $item['producto_id'],
                    'cantidad'     => $item['cantidad'],
                    'indicaciones' => $item['indicaciones'] ?? null,
                ]);
            }

            return redirect()->route('recetas.show', $receta)
                ->with('success', "Receta {$receta->codigo} registrada.");
        });
    }

    public function show(Receta $receta)
    {
        $receta->load(['cliente', 'farmaceutico', 'detalles.producto']);
        return view('recetas.show', compact('receta'));
    }

    public function destroy(Receta $receta)
    {
        $receta->delete();
        return redirect()->route('recetas.index')->with('success', 'Receta eliminada.');
    }
}
