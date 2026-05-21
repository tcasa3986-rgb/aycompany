@extends('reportes.pdf._layout')
@section('title', 'Reporte de ventas')
@section('subtitle', 'Periodo: ' . $desde->format('d/m/Y') . ' al ' . $hasta->format('d/m/Y'))
@section('content')
    <table>
        <thead>
            <tr>
                <th>Comprobante</th><th>Fecha</th><th>Cliente</th><th>Cajero</th><th>Pago</th>
                <th class="right">Subtotal</th><th class="right">IGV</th><th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $v)
                <tr>
                    <td>{{ $v->codigo }}</td>
                    <td>{{ $v->fecha?->format('d/m/Y H:i') }}</td>
                    <td>{{ $v->cliente?->nombre_completo ?? 'Genérico' }}</td>
                    <td>{{ $v->cajero?->name }}</td>
                    <td>{{ ucfirst($v->forma_pago) }}</td>
                    <td class="right">{{ number_format($v->subtotal, 2) }}</td>
                    <td class="right">{{ number_format($v->impuesto, 2) }}</td>
                    <td class="right">{{ number_format($v->total, 2) }}</td>
                </tr>
            @endforeach
            <tr class="totals">
                <td colspan="5">TOTALES ({{ $totales['count'] }} boletas)</td>
                <td class="right">{{ number_format($totales['subtotal'], 2) }}</td>
                <td class="right">{{ number_format($totales['impuesto'], 2) }}</td>
                <td class="right">S/ {{ number_format($totales['total'], 2) }}</td>
            </tr>
        </tbody>
    </table>
@endsection
