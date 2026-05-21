<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas {{ $year }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; color: #2d3748; }
        .header p { margin: 5px 0 0; color: #718096; }
        .kpi-container { display: table; width: 100%; margin-bottom: 20px; }
        .kpi-card { display: table-cell; width: 33.33%; text-align: center; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; }
        .kpi-label { color: #718096; font-size: 10px; text-transform: uppercase; margin-bottom: 5px; }
        .kpi-value { font-size: 16px; font-weight: bold; color: #2d3748; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f7fafc; color: #4a5568; font-weight: bold; text-align: left; padding: 10px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 9px; color: #a0aec0; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte Anual de Ventas {{ $year }}</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="kpi-container">
        <div class="kpi-card">
            <div class="kpi-label">Ingresos Totales</div>
            <div class="kpi-value" style="color: #48bb78;">{{ $globalSym }} {{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="kpi-card" style="border-left: 0; border-right: 0;">
            <div class="kpi-label">Cotizaciones Aprobadas</div>
            <div class="kpi-value">{{ $totalApproved }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Total Cotizaciones</div>
            <div class="kpi-value">{{ $totalQuotations }}</div>
        </div>
    </div>

    <h2 style="font-size: 14px; color: #4a5568; margin-top: 30px;">Detalle de Operaciones ({{ $year }})</h2>
    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Fecha Emisión</th>
                <th style="text-align:right">Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotations as $q)
            <tr>
                <td style="font-weight:bold;">{{ $q->quotation_number }}</td>
                <td>{{ $q->client->name ?? '—' }}</td>
                <td>{{ $q->issue_date->format('d/m/Y') }}</td>
                <td style="text-align:right; font-weight:bold;">{{ number_format($q->total, 2) }}</td>
                <td>{{ $q->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema CotizaPro - Página <span class="pagenum"></span>
    </div>
</body>
</html>
