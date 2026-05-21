<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\MovimientoCaja;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CuentaCorrienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::where('saldo_deudor', '>', 0)
            ->orWhere('limite_credito', '>', 0)
            ->orderByDesc('saldo_deudor')
            ->paginate(20);

        return view('clientes.cuentas.index', compact('clientes'));
    }

    public function show(Cliente $cliente)
    {
        $ventasPendientes = Venta::where('cliente_id', $cliente->id)
            ->where('forma_pago', 'credito')
            ->where('estado', 'emitida')
            ->orderByDesc('fecha')
            ->get();

        return view('clientes.cuentas.show', compact('cliente', 'ventasPendientes'));
    }

    public function abono(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'monto' => ['required', 'numeric', 'min:0.10', "max:{$cliente->saldo_deudor}"],
            'forma_pago' => ['required', 'in:efectivo,tarjeta,transferencia'],
        ]);

        $caja = Caja::where('user_id', Auth::id())->where('estado', 'abierta')->first();
        if (!$caja && $data['forma_pago'] === 'efectivo') {
            return back()->with('error', 'Debes abrir caja para recibir abonos en efectivo.');
        }

        DB::transaction(function () use ($cliente, $data, $caja) {
            $cliente->decrement('saldo_deudor', $data['monto']);

            if ($data['forma_pago'] === 'efectivo') {
                MovimientoCaja::create([
                    'caja_id'  => $caja->id,
                    'tipo'     => 'ingreso',
                    'monto'    => $data['monto'],
                    'concepto' => "Abono a cuenta: {$cliente->nombre_completo}",
                ]);
            }
        });

        return back()->with('success', 'Abono registrado correctamente.');
    }
}
