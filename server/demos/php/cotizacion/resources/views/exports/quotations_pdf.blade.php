<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Cotizaciones</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #2d3748; }
        .header p { margin: 5px 0 0; color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f7fafc; color: #4a5568; font-weight: bold; text-align: left; padding: 6px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 6px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .status { padding: 2px 5px; border-radius: 3px; font-weight: bold; text-transform: uppercase; font-size: 8px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 9px; color: #a0aec0; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Listado de Cotizaciones</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Emisión</th>
                <th>Vencimiento</th>
                <th>Moneda</th>
                <th style="text-align:right">Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotations as $q)
            <tr>
                <td style="font-weight:bold; color:#2b6cb0;">{{ $q->quotation_number }}</td>
                <td>{{ $q->client->name ?? '—' }}</td>
                <td>{{ $q->issue_date->format('d/m/Y') }}</td>
                <td>{{ $q->due_date ? $q->due_date->format('d/m/Y') : '—' }}</td>
                <td>{{ $q->currency }}</td>
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
