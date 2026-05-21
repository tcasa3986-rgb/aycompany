<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de ventas</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; }
        body { margin: 20px; }
        h1 { font-size: 16px; color: #0d6efd; margin: 0 0 10px; }
        .meta { color:#666; margin-bottom: 15px; }
        table { width:100%; border-collapse: collapse; }
        th { background:#0d6efd; color:#fff; padding:5px; text-align:left; }
        td { padding:5px; border-bottom:1px solid #eee; }
        tfoot td { font-weight:bold; background:#f8f9fa; border-top:2px solid #0d6efd; }
        .text-right { text-align:right; }
    </style>
</head>
<body>
    <h1>{{ $empresa['nombre'] }} - Reporte de Ventas</h1>
    <div class="meta">Período: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }} | Generado: {{ now()->format('d/m/Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th>N° Pedido</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Pago</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $p)
            <tr>
                <td>{{ $p->numero }}</td>
                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $p->cliente->nombre ?? '—' }}</td>
                <td>{{ ucfirst($p->tipo_pago) }}</td>
                <td class="text-right">S/ {{ number_format($p->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">TOTAL ({{ $totales['pedidos'] }} pedidos)</td>
                <td class="text-right">S/ {{ number_format($totales['monto'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
