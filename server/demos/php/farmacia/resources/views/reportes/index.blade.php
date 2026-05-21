@extends('layouts.app')
@section('title', 'Reportes')
@section('section', 'Reportes')

@section('content')
<div class="card card-pad">
    <h2 class="text-lg font-semibold text-gray-700 mb-2">Reportes</h2>
    <p class="text-sm text-gray-500 mb-6">Información gerencial con filtros y exportación a PDF / Excel.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('reportes.ventas') }}" class="rounded-xl border border-farmacia-200 p-6 hover:bg-farmacia-50 transition">
            <div class="text-3xl mb-2">💰</div>
            <p class="font-semibold text-farmacia-700">Ventas por periodo</p>
            <p class="text-xs text-gray-500 mt-1">Detalle de boletas y totales en un rango de fechas.</p>
        </a>
        <a href="{{ route('reportes.top') }}" class="rounded-xl border border-farmacia-200 p-6 hover:bg-farmacia-50 transition">
            <div class="text-3xl mb-2">🏆</div>
            <p class="font-semibold text-farmacia-700">Top productos</p>
            <p class="text-xs text-gray-500 mt-1">Productos más vendidos en el periodo.</p>
        </a>
        <a href="{{ route('reportes.stock') }}" class="rounded-xl border border-farmacia-200 p-6 hover:bg-farmacia-50 transition">
            <div class="text-3xl mb-2">⚠️</div>
            <p class="font-semibold text-farmacia-700">Stock crítico</p>
            <p class="text-xs text-gray-500 mt-1">Productos por debajo del mínimo.</p>
        </a>
        <a href="{{ route('reportes.vencer') }}" class="rounded-xl border border-farmacia-200 p-6 hover:bg-farmacia-50 transition">
            <div class="text-3xl mb-2">📅</div>
            <p class="font-semibold text-farmacia-700">Próximos a vencer</p>
            <p class="text-xs text-gray-500 mt-1">Lotes con vencimiento próximo.</p>
        </a>
    </div>
</div>
@endsection
