<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Clientes</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #2d3748; }
        .header p { margin: 5px 0 0; color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f7fafc; color: #4a5568; font-weight: bold; text-align: left; padding: 8px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 9px; color: #a0aec0; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Listado de Clientes</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre / Razón Social</th>
                <th>RUC / DNI</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th style="text-align:center">Cotizaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $c)
            <tr>
                <td style="font-weight:bold">{{ $c->name }}</td>
                <td>{{ $c->document_number ?: '—' }}</td>
                <td>{{ $c->email ?: '—' }}</td>
                <td>{{ $c->phone ?: '—' }}</td>
                <td>{{ $c->address ?: '—' }}</td>
                <td style="text-align:center">{{ $c->quotations_count ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema CotizaPro - Página <span class="pagenum"></span>
    </div>
</body>
</html>
