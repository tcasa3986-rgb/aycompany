<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Caja Diaria</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0077b6; padding-bottom: 10px; }
        .header h1 { color: #0077b6; margin: 0; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 5px 0; color: #555; }
        
        .section-title { font-size: 14px; color: #00b4d8; border-bottom: 1px solid #ccc; margin-top: 20px; padding-bottom: 5px; margin-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #f4f6f9; color: #333; text-align: left; padding: 8px; border: 1px solid #ddd; font-size: 11px; }
        td { padding: 8px; border: 1px solid #ddd; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .summary-box { width: 300px; float: right; border: 1px solid #0077b6; padding: 15px; background-color: #f9fbfe; }
        .summary-row { display: block; margin-bottom: 8px; font-size: 13px; }
        .summary-label { display: inline-block; width: 150px; font-weight: bold; }
        .summary-value { display: inline-block; width: 110px; text-align: right; }
        .summary-total { border-top: 2px solid #0077b6; padding-top: 10px; margin-top: 5px; font-weight: bold; font-size: 16px; color: #0077b6; }
        
        .footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 30px; border-top: 1px solid #ddd; text-align: center; padding-top: 5px; font-size: 10px; color: #777; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ $labNombre }}</h1>
    <p><strong>Reporte de Ingresos de Caja Diaria</strong></p>
    <p>Fecha de reporte: {{ $hoy->format('d/m/Y') }}</p>
</div>

<div class="section-title">Facturas Emitidas en el Día</div>
<table>
    <thead>
        <tr>
            <th>N° Factura</th>
            <th>Hora</th>
            <th>Paciente</th>
            <th>Orden Ref.</th>
            <th>Estado</th>
            <th class="text-right">Total Facturado (S/)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($facturasHoy as $factura)
        <tr>
            <td>{{ $factura->numero_factura }}</td>
            <td class="text-center">{{ $factura->created_at->format('H:i') }}</td>
            <td>{{ $factura->orden->paciente->nombre_completo ?? 'N/A' }}</td>
            <td class="text-center">{{ $factura->orden->numero_orden ?? 'N/A' }}</td>
            <td class="text-center">{{ $factura->estado }}</td>
            <td class="text-right">{{ number_format($factura->total, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">No se emitieron facturas en el día.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">Detalle de Pagos Recibidos</div>
<table>
    <thead>
        <tr>
            <th>ID Pago</th>
            <th>Hora</th>
            <th>Medio de Pago</th>
            <th>N° Factura</th>
            <th>Referencia</th>
            <th class="text-right">Monto Recibido (S/)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pagosHoy as $pago)
        <tr>
            <td>#{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td class="text-center">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('H:i') }}</td>
            <td class="text-center">{{ $pago->medio_pago }}</td>
            <td class="text-center">{{ $pago->factura->numero_factura ?? 'N/A' }}</td>
            <td>{{ $pago->referencia ?? '-' }}</td>
            <td class="text-right">{{ number_format($pago->monto, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">No se registraron pagos en el día.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="clearfix">
    <div class="summary-box">
        <div class="summary-row">
            <span class="summary-label">Total Facturado:</span>
            <span class="summary-value">S/ {{ number_format($totalFacturado, 2) }}</span>
        </div>
        <div class="summary-row summary-total">
            <span class="summary-label" style="font-size: 14px;">Ingresos en Caja:</span>
            <span class="summary-value">S/ {{ number_format($totalCobrado, 2) }}</span>
        </div>
    </div>
</div>

<div class="footer">
    Reporte generado automáticamente por LabSalud el {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
