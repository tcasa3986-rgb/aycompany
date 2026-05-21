<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Factura;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $query = Factura::with(['orden.paciente', 'user']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('numero_factura', 'like', "%{$search}%")
                  ->orWhereHas('orden.paciente', function($q) use ($search) {
                      $q->where('nombres', 'like', "%{$search}%")
                        ->orWhere('numero_documento', 'like', "%{$search}%");
                  });
        }

        $facturas = $query->latest()->paginate(10);

        // Órdenes pendientes de pago
        $ordenesPendientes = Orden::with('paciente')
            ->where('pagado', false)
            ->whereIn('estado', ['Completado', 'Entregado', 'Pendiente', 'En proceso'])
            ->latest()
            ->take(10)
            ->get();

        return view('facturas.index', compact('facturas', 'ordenesPendientes'));
    }

    public function show(Factura $factura)
    {
        $factura->load(['orden.paciente', 'orden.detalles.prueba', 'orden.convenio', 'pagos', 'user']);
        return view('facturas.show', compact('factura'));
    }

    public function create(Request $request)
    {
        $request->validate(['orden_id' => 'required|exists:ordenes,id']);

        $orden = Orden::with(['paciente', 'convenio', 'detalles.prueba'])->findOrFail($request->orden_id);

        if ($orden->pagado) {
            return redirect()->route('facturas.index')->with('error', 'Esta orden ya se encuentra pagada.');
        }

        return view('facturas.create', compact('orden'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orden_id'         => 'required|exists:ordenes,id',
            'tipo_comprobante' => 'required|in:Boleta,Factura',
            'medio_pago'       => 'required|string',
            'monto_recibido'   => 'required|numeric|min:0',
        ]);

        $orden = Orden::findOrFail($request->orden_id);

        if ($orden->pagado) {
            return back()->with('error', 'La orden ya ha sido pagada previamente.');
        }

        if ($request->monto_recibido < $orden->total) {
            return back()->with('error', 'El monto recibido es menor al total a pagar.');
        }

        try {
            DB::beginTransaction();

            // Número de comprobante secuencial
            $prefijo = strtoupper(substr($request->tipo_comprobante, 0, 1));
            $ultimo  = Factura::where('tipo_comprobante', $request->tipo_comprobante)->count();
            $numero  = $prefijo . '-' . str_pad($ultimo + 1, 8, '0', STR_PAD_LEFT);

            // IGV solo en Facturas (el total ya incluye IGV en boletas)
            $igv = ($request->tipo_comprobante === 'Factura')
                ? round($orden->total - ($orden->total / 1.18), 2)
                : 0;

            $factura = Factura::create([
                'orden_id'         => $orden->id,
                'numero_factura'   => $numero,
                'tipo_comprobante' => $request->tipo_comprobante,
                'convenio_id'      => $orden->convenio_id,
                'subtotal'         => $orden->subtotal,
                'descuento'        => $orden->descuento,
                'igv'              => $igv,
                'total'            => $orden->total,
                'estado'           => 'Pagada',
                'user_id'          => auth()->id(),
            ]);

            Pago::create([
                'factura_id' => $factura->id,
                'monto'      => $orden->total,
                'medio_pago' => $request->medio_pago,
                'referencia' => $request->referencia,
                'fecha_pago' => now(),
                'user_id'    => auth()->id(),
            ]);

            $orden->update(['pagado' => true]);

            DB::commit();

            return redirect()->route('facturas.show', $factura->id)
                             ->with('success', "Comprobante {$factura->numero_factura} generado correctamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }
}
