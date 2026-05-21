<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReporteController extends Controller
{
    public function resultadoPdf(Orden $orden)
    {
        // Verificar si la orden tiene resultados completados
        if ($orden->estado !== 'Completado' && $orden->estado !== 'Entregado') {
            return back()->with('error', 'Los resultados de la orden aún no han sido validados completamente.');
        }

        $orden->load(['paciente', 'medico', 'detalles.prueba.area', 'detalles.resultado']);

        // Generar un validador QR
        $urlValidacion = url("/validar-resultado/{$orden->numero_orden}");
        $qrcode = base64_encode(QrCode::format('svg')->size(100)->generate($urlValidacion));

        $pdf = Pdf::loadView('resultados.pdf', compact('orden', 'qrcode'))->setPaper('A4', 'portrait');
        
        return $pdf->stream("Resultado_Lab_{$orden->paciente->numero_documento}_{$orden->numero_orden}.pdf");
    }

    public function validarResultado(string $numero)
    {
        $orden = \App\Models\Orden::with(['paciente', 'detalles.prueba.area', 'detalles.resultado'])
            ->where('numero_orden', $numero)
            ->first();

        if (!$orden) {
            abort(404, 'Resultado no encontrado.');
        }

        return view('resultados.validar', compact('orden'));
    }

    public function enviarEmail(\App\Models\Orden $orden)
    {
        if (!$orden->paciente->email) {
            return back()->with('error', 'El paciente no tiene un correo electrónico registrado.');
        }

        $orden->load(['paciente', 'medico', 'detalles.prueba.area', 'detalles.resultado']);
        $configuraciones = \App\Models\Configuracion::pluck('valor', 'clave')->toArray();

        $pdf = \PDF::loadView('reportes.resultado_pdf', compact('orden', 'configuraciones'));
        
        \Illuminate\Support\Facades\Mail::to($orden->paciente->email)
            ->send(new \App\Mail\ResultadoPruebaMail($orden, $pdf->output()));

        return back()->with('success', 'El resultado ha sido enviado por correo electrónico al paciente.');
    }

    public function cajaDiariaPdf()
    {
        $hoy = \Carbon\Carbon::today();
        
        $facturasHoy = \App\Models\Factura::with(['orden.paciente'])
            ->whereDate('created_at', $hoy)
            ->get();
            
        $pagosHoy = \App\Models\Pago::with('factura')
            ->whereDate('fecha_pago', $hoy)
            ->get();
            
        $totalFacturado = $facturasHoy->sum('total');
        $totalCobrado = $pagosHoy->sum('monto');
        
        $configuraciones = \App\Models\Configuracion::pluck('valor', 'clave')->toArray();
        $labNombre = $configuraciones['lab_nombre'] ?? 'Laboratorio Clínico';
        
        $pdf = \PDF::loadView('reportes.caja_diaria_pdf', compact(
            'facturasHoy', 'pagosHoy', 'totalFacturado', 'totalCobrado', 'hoy', 'labNombre'
        ));
        
        return $pdf->stream("Reporte_Caja_" . $hoy->format('Y-m-d') . ".pdf");
    }
}
