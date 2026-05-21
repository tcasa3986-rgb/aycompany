<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::with('usuario')->orderByDesc('apertura')->paginate(15);
        $cajaAbierta = Caja::where('user_id', Auth::id())->where('estado', 'abierta')->first();
        return view('cajas.index', compact('cajas', 'cajaAbierta'));
    }

    public function abrir(Request $request)
    {
        $existing = Caja::where('user_id', Auth::id())->where('estado', 'abierta')->first();
        if ($existing) {
            return redirect()->route('cajas.show', $existing)->with('error', 'Ya tienes una caja abierta.');
        }

        $data = $request->validate([
            'monto_apertura' => ['required', 'numeric', 'min:0'],
            'observaciones'  => ['nullable', 'string', 'max:300'],
        ]);

        $caja = Caja::create([
            'user_id'        => Auth::id(),
            'monto_apertura' => $data['monto_apertura'],
            'apertura'       => now(),
            'estado'         => 'abierta',
            'observaciones'  => $data['observaciones'] ?? null,
        ]);

        return redirect()->route('cajas.show', $caja)->with('success', 'Caja abierta correctamente.');
    }

    public function show(Caja $caja)
    {
        $caja->load(['usuario', 'movimientos']);
        $ventas = $caja->ventas()->where('estado', 'emitida')->get();
        return view('cajas.show', compact('caja', 'ventas'));
    }

    public function movimiento(Request $request, Caja $caja)
    {
        abort_if($caja->estado !== 'abierta', 422, 'Caja cerrada.');

        $data = $request->validate([
            'tipo'     => ['required', 'in:ingreso,egreso'],
            'monto'    => ['required', 'numeric', 'min:0.01'],
            'concepto' => ['required', 'string', 'max:200'],
        ]);

        MovimientoCaja::create([
            'caja_id'  => $caja->id,
            'tipo'     => $data['tipo'],
            'monto'    => $data['monto'],
            'concepto' => $data['concepto'],
        ]);

        return back()->with('success', 'Movimiento registrado.');
    }

    public function cerrar(Request $request, Caja $caja)
    {
        abort_if($caja->estado !== 'abierta', 422, 'Caja ya cerrada.');

        $data = $request->validate([
            'monto_cierre' => ['required', 'numeric', 'min:0'],
            'observaciones'=> ['nullable', 'string', 'max:300'],
        ]);

        DB::transaction(function () use ($caja, $data) {
            $totalVentas = (float) $caja->ventas()->where('estado', 'emitida')->sum('total');
            $caja->update([
                'monto_cierre'  => $data['monto_cierre'],
                'total_ventas'  => $totalVentas,
                'cierre'        => now(),
                'estado'        => 'cerrada',
                'observaciones' => trim(($caja->observaciones ?? '') . "\nCierre: " . ($data['observaciones'] ?? '')),
            ]);
        });

        return redirect()->route('cajas.show', $caja)->with('success', 'Caja cerrada y cuadrada.');
    }
}
