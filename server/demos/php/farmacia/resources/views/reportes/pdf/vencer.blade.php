@extends('reportes.pdf._layout')
@section('title', 'Lotes próximos a vencer')
@section('subtitle', 'Próximos ' . $dias . ' días · Generado ' . now()->format('d/m/Y'))
@section('content')
    <table>
        <thead><tr><th>Código</th><th>Producto</th><th>Lote</th><th>Vencimiento</th><th class="right">Cantidad</th></tr></thead>
        <tbody>
            @foreach($lotes as $l)
                <tr>
                    <td>{{ $l->codigo }}</td>
                    <td>{{ $l->nombre }}</td>
                    <td>{{ $l->numero_lote }}</td>
                    <td>{{ \Carbon\Carbon::parse($l->fecha_vencimiento)->format('d/m/Y') }}</td>
                    <td class="right">{{ $l->cantidad }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
