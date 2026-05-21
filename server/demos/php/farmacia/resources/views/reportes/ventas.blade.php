@extends('layouts.app')
@section('title', 'Reporte de ventas')
@section('section', 'Reportes')

@section('content')
<div class="card card-pad">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-700">Ventas por periodo</h2>
        <a href="{{ route('reportes.index') }}" class="btn-secondary">← Reportes</a>
    </div>

    @include('reportes._filtros', ['route' => 'reportes.ventas', 'desde' => $desde, 'hasta' => $hasta])

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
        <div class="rounded-xl bg-farmacia-50 p-4">
            <p class="text-xs uppercase text-farmacia-700 font-semibold tracking-wider">Boletas</p>
            <p class="text-xl font-bold text-farmacia-700 mt-1">{{ $totales['count'] }}</p>
        </div>
        <div class="rounded-xl bg-gray-50 p-4">
            <p class="text-xs uppercase text-gray-500 font-semibold tracking-wider">Subtotal</p>
            <p class="text-xl font-bold text-gray-700 mt-1">S/ {{ number_format($totales['subtotal'], 2) }}</p>
        </div>
        <div class="rounded-xl bg-rose-50 p-4">
            <p class="text-xs uppercase text-rose-700 font-semibold tracking-wider">Descuento</p>
            <p class="text-xl font-bold text-rose-700 mt-1">S/ {{ number_format($totales['descuento'], 2) }}</p>
        </div>
        <div class="rounded-xl bg-sky-50 p-4">
            <p class="text-xs uppercase text-sky-700 font-semibold tracking-wider">IGV</p>
            <p class="text-xl font-bold text-sky-700 mt-1">S/ {{ number_format($totales['impuesto'], 2) }}</p>
        </div>
        <div class="rounded-xl bg-emerald-50 p-4">
            <p class="text-xs uppercase text-emerald-700 font-semibold tracking-wider">Total</p>
            <p class="text-xl font-bold text-emerald-700 mt-1">S/ {{ number_format($totales['total'], 2) }}</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>Comprobante</th><th>Fecha</th><th>Cliente</th><th>Cajero</th><th>Pago</th>
                <th class="text-right">Subtotal</th><th class="text-right">IGV</th><th class="text-right">Total</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($ventas as $v)
                    <tr>
                        <td class="font-mono text-xs">{{ $v->codigo }}</td>
                        <td>{{ $v->fecha?->format('d/m/Y H:i') }}</td>
                        <td>{{ $v->cliente?->nombre_completo ?? 'Genérico' }}</td>
                        <td>{{ $v->cajero?->name }}</td>
                        <td class="capitalize">{{ $v->forma_pago }}</td>
                        <td class="text-right">S/ {{ number_format($v->subtotal, 2) }}</td>
                        <td class="text-right">S/ {{ number_format($v->impuesto, 2) }}</td>
                        <td class="text-right font-semibold">S/ {{ number_format($v->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-6 text-center text-gray-400">Sin ventas en el rango.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
