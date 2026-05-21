<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Configuracion;
use Barryvdh\DomPDF\Facade\Pdf;

class ComprobanteController extends Controller
{
    /**
     * Genera ticket de impresión 80mm para un pedido.
     */
    public function ticket(Pedido $pedido)
    {
        $pedido->load(['cliente', 'items.producto', 'repartidor', 'pagos']);
        $empresa = $this->datosEmpresa();

        $pdf = Pdf::loadView('comprobantes.ticket', compact('pedido', 'empresa'))
            ->setPaper([0, 0, 226.77, 600], 'portrait'); // 80mm de ancho

        return $pdf->stream("ticket-{$pedido->numero}.pdf");
    }

    /**
     * Genera boleta/factura A4 para un pedido.
     */
    public function comprobante(Pedido $pedido)
    {
        $pedido->load(['cliente', 'items.producto', 'repartidor', 'pagos']);
        $empresa = $this->datosEmpresa();

        $pdf = Pdf::loadView('comprobantes.boleta', compact('pedido', 'empresa'))
            ->setPaper('a4');

        return $pdf->stream("comprobante-{$pedido->numero}.pdf");
    }

    /**
     * Reporte ventas por rango de fechas en PDF.
     */
    public function reporteVentas(\Illuminate\Http\Request $request)
    {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $pedidos = Pedido::with('cliente')
            ->whereBetween('created_at', [$desde.' 00:00:00', $hasta.' 23:59:59'])
            ->where('estado', 'entregado')
            ->orderBy('created_at')
            ->get();

        $empresa = $this->datosEmpresa();
        $totales = [
            'pedidos' => $pedidos->count(),
            'monto'   => $pedidos->sum('total'),
        ];

        $pdf = Pdf::loadView('comprobantes.reporte_ventas', compact('pedidos', 'empresa', 'desde', 'hasta', 'totales'));
        return $pdf->stream("ventas-{$desde}-{$hasta}.pdf");
    }

    private function datosEmpresa(): array
    {
        return [
            'nombre'    => Configuracion::obtener('empresa_nombre', 'Mi Delivery'),
            'ruc'       => Configuracion::obtener('empresa_ruc', ''),
            'direccion' => Configuracion::obtener('empresa_direccion', ''),
            'telefono'  => Configuracion::obtener('empresa_telefono', ''),
            'email'     => Configuracion::obtener('empresa_email', ''),
            'logo'      => Configuracion::obtener('empresa_logo', ''),
        ];
    }
}
