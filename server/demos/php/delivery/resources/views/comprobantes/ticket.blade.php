<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket {{ $pedido->numero }}</title>
    <style>
        * { font-family: 'DejaVu Sans Mono', monospace; font-size: 10px; }
        body { margin: 0; padding: 5px; }
        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: bold; }
        .lg     { font-size: 13px; }
        hr      { border: 0; border-top: 1px dashed #000; margin: 4px 0; }
        table   { width: 100%; border-collapse: collapse; }
        th, td  { padding: 2px 0; }
    </style>
</head>
<body>
    <div class="center bold lg">{{ $empresa['nombre'] }}</div>
    @if($empresa['ruc'])<div class="center">RUC: {{ $empresa['ruc'] }}</div>@endif
    @if($empresa['direccion'])<div class="center">{{ $empresa['direccion'] }}</div>@endif
    @if($empresa['telefono'])<div class="center">Tel: {{ $empresa['telefono'] }}</div>@endif

    <hr>
    <div class="center bold">PEDIDO {{ $pedido->numero }}</div>
    <hr>
    <div>Fecha: {{ $pedido->created_at->format('d/m/Y H:i') }}</div>
    <div>Cliente: {{ $pedido->cliente->nombre }}</div>
    @if($pedido->cliente->telefono)<div>Tel: {{ $pedido->cliente->telefono }}</div>@endif
    <div>Direccion: {{ $pedido->direccion_entrega }}</div>
    @if($pedido->repartidor)<div>Repartidor: {{ $pedido->repartidor->nombre }}</div>@endif

    <hr>
    <table>
        <tr class="bold">
            <td style="width:10%">Cnt</td>
            <td style="width:55%">Producto</td>
            <td class="right" style="width:35%">Total</td>
        </tr>
        @foreach($pedido->items as $i)
        <tr>
            <td>{{ $i->cantidad }}</td>
            <td>{{ $i->nombre_producto }}</td>
            <td class="right">{{ number_format($i->subtotal, 2) }}</td>
        </tr>
        @if($i->notas)<tr><td colspan="3" style="font-size:9px; color:#555">  > {{ $i->notas }}</td></tr>@endif
        @endforeach
    </table>

    <hr>
    <table>
        <tr><td>Subtotal</td><td class="right">S/ {{ number_format($pedido->subtotal, 2) }}</td></tr>
        <tr><td>Delivery</td><td class="right">S/ {{ number_format($pedido->costo_delivery, 2) }}</td></tr>
        @if($pedido->descuento > 0)
        <tr><td>Descuento</td><td class="right">- S/ {{ number_format($pedido->descuento, 2) }}</td></tr>
        @endif
        <tr class="bold lg"><td>TOTAL</td><td class="right">S/ {{ number_format($pedido->total, 2) }}</td></tr>
    </table>

    <hr>
    <div>Pago: {{ ucfirst($pedido->tipo_pago) }} - {{ ucfirst($pedido->estado_pago) }}</div>
    @if($pedido->notas)<div style="margin-top:4px">Notas: {{ $pedido->notas }}</div>@endif

    <hr>
    <div class="center">¡Gracias por su compra!</div>
    <div class="center" style="font-size:9px">{{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>
