<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante {{ $pedido->numero }}</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color:#222; }
        body { margin: 25px; }
        .row { width: 100%; }
        .col-half { display: inline-block; width: 49%; vertical-align: top; }
        .header { border-bottom: 3px solid #0d6efd; padding-bottom: 10px; margin-bottom: 15px; }
        .title { font-size: 20px; font-weight: bold; color: #0d6efd; }
        .box { border: 1px solid #ddd; padding: 8px 12px; border-radius: 4px; margin-bottom: 10px; }
        .label { color: #666; font-size: 10px; text-transform: uppercase; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.items th { background:#0d6efd; color:#fff; padding:6px; text-align:left; }
        table.items td { padding:6px; border-bottom:1px solid #eee; }
        .text-right { text-align: right; }
        .totals { width: 45%; float: right; margin-top: 10px; }
        .totals td { padding: 4px 8px; }
        .totals .total { font-size: 14px; font-weight: bold; background: #0d6efd; color: #fff; }
        .footer { margin-top: 30px; text-align:center; font-size: 9px; color:#888; border-top:1px dashed #ccc; padding-top: 10px; }
        .estado { display: inline-block; padding: 3px 10px; border-radius: 4px; font-weight: bold; font-size: 10px; color:#fff; background:#198754; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width:100%"><tr>
            <td style="width:60%">
                <div class="title">{{ $empresa['nombre'] }}</div>
                @if($empresa['ruc'])<div>RUC: {{ $empresa['ruc'] }}</div>@endif
                @if($empresa['direccion'])<div>{{ $empresa['direccion'] }}</div>@endif
                @if($empresa['telefono'])<div>Tel: {{ $empresa['telefono'] }} @if($empresa['email']) | {{ $empresa['email'] }}@endif</div>@endif
            </td>
            <td style="width:40%; text-align:right">
                <div style="font-size:14px; font-weight:bold; border:2px solid #0d6efd; padding:8px;">
                    COMPROBANTE DE VENTA<br>
                    <span style="color:#0d6efd; font-size:18px">{{ $pedido->numero }}</span>
                </div>
                <div style="margin-top:6px">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
            </td>
        </tr></table>
    </div>

    <div class="row">
        <div class="col-half">
            <div class="box">
                <div class="label">Cliente</div>
                <strong>{{ $pedido->cliente->nombre }}</strong><br>
                @if($pedido->cliente->documento) DNI/RUC: {{ $pedido->cliente->documento }}<br>@endif
                @if($pedido->cliente->telefono) Tel: {{ $pedido->cliente->telefono }}<br>@endif
                @if($pedido->cliente->email) {{ $pedido->cliente->email }}@endif
            </div>
        </div>
        <div class="col-half">
            <div class="box">
                <div class="label">Entrega</div>
                {{ $pedido->direccion_entrega }}<br>
                @if($pedido->referencia_entrega)<small>Ref: {{ $pedido->referencia_entrega }}</small><br>@endif
                @if($pedido->repartidor)Repartidor: <strong>{{ $pedido->repartidor->nombre }}</strong>@endif
            </div>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:10%">Cant</th>
                <th>Producto</th>
                <th style="width:20%" class="text-right">P. Unit</th>
                <th style="width:20%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->items as $i)
            <tr>
                <td>{{ $i->cantidad }}</td>
                <td>{{ $i->nombre_producto }}@if($i->notas)<br><small style="color:#888">{{ $i->notas }}</small>@endif</td>
                <td class="text-right">S/ {{ number_format($i->precio_unitario, 2) }}</td>
                <td class="text-right">S/ {{ number_format($i->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal:</td><td class="text-right">S/ {{ number_format($pedido->subtotal,2) }}</td></tr>
        <tr><td>Delivery:</td><td class="text-right">S/ {{ number_format($pedido->costo_delivery,2) }}</td></tr>
        @if($pedido->descuento > 0)
        <tr><td>Descuento:</td><td class="text-right">- S/ {{ number_format($pedido->descuento,2) }}</td></tr>
        @endif
        <tr class="total"><td>TOTAL:</td><td class="text-right">S/ {{ number_format($pedido->total,2) }}</td></tr>
    </table>

    <div style="clear:both"></div>

    <div class="box" style="margin-top:20px">
        <div class="label">Forma de pago</div>
        {{ ucfirst($pedido->tipo_pago) }}
        <span class="estado" style="background:{{ $pedido->estado_pago === 'pagado' ? '#198754' : '#fd7e14' }}">
            {{ strtoupper($pedido->estado_pago) }}
        </span>
        @if($pedido->notas)<div style="margin-top:6px"><small>{{ $pedido->notas }}</small></div>@endif
    </div>

    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i') }} - {{ $empresa['nombre'] }}
    </div>
</body>
</html>
