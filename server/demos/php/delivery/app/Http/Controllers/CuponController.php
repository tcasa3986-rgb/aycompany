<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CuponController extends Controller
{
    public function index()
    {
        $cupones = Cupon::orderByDesc('activo')->orderBy('valido_hasta')->paginate(20);
        return view('cupones.index', compact('cupones'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        Cupon::create($data);
        return back()->with('success', 'Cupón creado.');
    }

    public function update(Request $request, Cupon $cupon)
    {
        $data = $this->validar($request, $cupon->id);
        $cupon->update($data);
        return back()->with('success', 'Cupón actualizado.');
    }

    public function destroy(Cupon $cupon)
    {
        if ($cupon->pedidos()->exists()) {
            $cupon->update(['activo' => false]);
            return back()->with('success', 'Cupón desactivado (tiene pedidos asociados).');
        }
        $cupon->delete();
        return back()->with('success', 'Cupón eliminado.');
    }

    /**
     * Validar cupón en tiempo real (AJAX) al crear pedido.
     */
    public function validar_codigo(Request $request)
    {
        $request->validate([
            'codigo'     => 'required|string',
            'subtotal'   => 'required|numeric|min:0',
            'cliente_id' => 'nullable|exists:clientes,id',
        ]);

        $cupon = Cupon::where('codigo', strtoupper($request->codigo))->first();
        if (!$cupon) {
            return response()->json(['ok' => false, 'mensaje' => 'Cupón no encontrado.'], 404);
        }
        try {
            $descuento = $cupon->calcularDescuento(
                (float) $request->subtotal,
                $request->cliente_id ? Cliente::find($request->cliente_id) : null
            );
            return response()->json([
                'ok'        => true,
                'cupon_id'  => $cupon->id,
                'codigo'    => $cupon->codigo,
                'descuento' => $descuento,
                'mensaje'   => "Descuento aplicado: S/ ".number_format($descuento, 2),
            ]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'mensaje' => $e->getMessage()], 422);
        }
    }

    private function validar(Request $request, $id = null): array
    {
        $data = $request->validate([
            'codigo'             => ['required','string','max:30', Rule::unique('cupones','codigo')->ignore($id)],
            'descripcion'        => 'nullable|string|max:200',
            'tipo'               => 'required|in:porcentaje,monto',
            'valor'              => 'required|numeric|min:0',
            'monto_minimo'       => 'nullable|numeric|min:0',
            'descuento_maximo'   => 'nullable|numeric|min:0',
            'usos_maximos'       => 'nullable|integer|min:1',
            'valido_desde'       => 'nullable|date',
            'valido_hasta'       => 'nullable|date|after_or_equal:valido_desde',
            'solo_primer_pedido' => 'boolean',
            'activo'             => 'boolean',
        ]);
        $data['codigo']            = strtoupper($data['codigo']);
        $data['solo_primer_pedido']= $request->boolean('solo_primer_pedido');
        $data['activo']            = $request->boolean('activo', true);
        return $data;
    }
}
