@extends('reportes.pdf._layout')
@section('title', 'Top productos vendidos')
@section('subtitle', 'Periodo: ' . $desde->format('d/m/Y') . ' al ' . $hasta->format('d/m/Y'))
@section('content')
    <table>
        <thead><tr><th>#</th><th>Código</th><th>Producto</th><th class="right">Cantidad</th><th class="right">Importe</th></tr></thead>
        <tbody>
            @foreach($top as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->codigo }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td class="right">{{ (int) $p->cantidad }}</td>
                    <td class="right">S/ {{ number_format($p->importe, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
