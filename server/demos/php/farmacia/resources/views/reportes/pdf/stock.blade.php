@extends('reportes.pdf._layout')
@section('title', 'Stock crítico')
@section('subtitle', 'Productos por debajo del mínimo · ' . now()->format('d/m/Y'))
@section('content')
    <table>
        <thead><tr><th>Código</th><th>Producto</th><th>Categoría</th><th class="right">Stock</th><th class="right">Mínimo</th><th class="right">Diferencia</th></tr></thead>
        <tbody>
            @foreach($productos as $p)
                <tr>
                    <td>{{ $p->codigo }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->categoria?->nombre ?? '—' }}</td>
                    <td class="right">{{ $p->stock }}</td>
                    <td class="right">{{ $p->stock_minimo }}</td>
                    <td class="right">{{ $p->stock - $p->stock_minimo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
