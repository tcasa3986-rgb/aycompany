<h1>Reporte de Alertas del Sistema</h1>
<p>Se han detectado las siguientes alertas que requieren su atención:</p>

@if(count($bajoStock) > 0)
    <h2>Productos con Stock Bajo</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bajoStock as $p)
                <tr>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>{{ $p->stock_minimo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if(count($porVencer) > 0)
    <h2>Lotes por Vencer (próximos 30 días)</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Código Lote</th>
                <th>Fecha Vencimiento</th>
            </tr>
        </thead>
        <tbody>
            @foreach($porVencer as $l)
                <tr>
                    <td>{{ $l->producto->nombre }}</td>
                    <td>{{ $l->codigo }}</td>
                    <td>{{ $l->fecha_vencimiento->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<p>Por favor, tome las medidas necesarias (reposición o retiro de productos).</p>
