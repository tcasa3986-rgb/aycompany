<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket {{ $venta->codigo }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; margin: 0; padding: 10px; width: 80mm; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; margin-top: 5px; padding-top: 5px; }
        .mb-1 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        .total-row { font-size: 14px; margin-top: 10px; }
        @media print {
            .no-print { display: none; }
            body { width: 80mm; }
        }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="bold" style="font-size: 16px;">{{ setting('company_name', 'ERP Farmacia') }}</div>
        <div>{{ setting('company_address') }}</div>
        <div>{{ setting('company_phone') }}</div>
        <div>RUC: {{ setting('company_tax_id') }}</div>
    </div>

    <div class="border-top mb-1">
        <div>{{ strtoupper($venta->tipo_comprobante) }}: {{ $venta->codigo }}</div>
        <div>Fecha: {{ $venta->fecha?->format('d/m/Y H:i') }}</div>
        <div>Cajero: {{ $venta->cajero?->name }}</div>
        <div>Cliente: {{ $venta->cliente?->nombre_completo ?? 'Cliente Genérico' }}</div>
    </div>

    <table class="border-top">
        <thead>
            <tr>
                <th class="text-left">Cant</th>
                <th class="text-left">Descrip</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $d)
                <tr>
                    <td>{{ $d->cantidad }}</td>
                    <td>{{ substr($d->producto->nombre, 0, 20) }}</td>
                    <td class="text-right">{{ number_format($d->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-top total-row">
        <div class="flex justify-between">
            <span class="bold">TOTAL:</span>
            <span class="bold text-right" style="float: right;">S/ {{ number_format($venta->total, 2) }}</span>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="mb-1" style="margin-top: 10px;">
        <div>Forma Pago: {{ ucfirst($venta->forma_pago) }}</div>
        <div>Efectivo: S/ {{ number_format($venta->pago_recibido, 2) }}</div>
        <div class="bold">Cambio: S/ {{ number_format($venta->cambio, 2) }}</div>
    </div>

    <div class="text-center border-top" style="margin-top: 20px;">
        <p>¡Gracias por su compra!</p>
        <p>Visite nuestra web: {{ config('app.url') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() { window.close(); }, 500);
        };
    </script>
</body>
</html>
