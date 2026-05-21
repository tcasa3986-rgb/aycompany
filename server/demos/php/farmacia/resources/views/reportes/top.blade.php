@extends('layouts.app')
@section('title', 'Top productos')
@section('section', 'Reportes')

@section('content')
<div class="card card-pad">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-700">Top productos vendidos</h2>
        <a href="{{ route('reportes.index') }}" class="btn-secondary">← Reportes</a>
    </div>

    @include('reportes._filtros', ['route' => 'reportes.top', 'desde' => $desde, 'hasta' => $hasta])

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th class="w-10">#</th>
                <th>Código</th>
                <th>Producto</th>
                <th class="text-right">Cantidad vendida</th>
                <th class="text-right">Importe</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($top as $i => $p)
                    <tr>
                        <td class="text-gray-400">{{ $i + 1 }}</td>
                        <td class="font-mono text-xs">{{ $p->codigo }}</td>
                        <td class="font-medium text-gray-800">{{ $p->nombre }}</td>
                        <td class="text-right">{{ (int) $p->cantidad }}</td>
                        <td class="text-right font-semibold">S/ {{ number_format($p->importe, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-gray-400">Sin datos en el rango.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
