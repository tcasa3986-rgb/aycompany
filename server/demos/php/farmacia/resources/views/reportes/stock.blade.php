@extends('layouts.app')
@section('title', 'Stock crítico')
@section('section', 'Reportes')

@section('content')
<div class="card card-pad">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-700">Stock crítico</h2>
        <a href="{{ route('reportes.index') }}" class="btn-secondary">← Reportes</a>
    </div>
    <p class="text-sm text-gray-500 mb-4">Productos con stock por debajo del mínimo configurado.</p>
    <div class="flex gap-2 mb-4">
        <a href="{{ route('reportes.stock', ['export' => 'pdf']) }}" class="btn-secondary">📄 PDF</a>
        <a href="{{ route('reportes.stock', ['export' => 'excel']) }}" class="btn-secondary">📊 Excel</a>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th class="text-right">Stock</th>
                <th class="text-right">Mínimo</th>
                <th class="text-right">Diferencia</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($productos as $p)
                    <tr>
                        <td class="font-mono text-xs">{{ $p->codigo }}</td>
                        <td class="font-medium text-gray-800">{{ $p->nombre }}</td>
                        <td>{{ $p->categoria?->nombre ?? '—' }}</td>
                        <td class="text-right">{{ $p->stock }}</td>
                        <td class="text-right">{{ $p->stock_minimo }}</td>
                        <td class="text-right font-semibold text-rose-600">{{ $p->stock - $p->stock_minimo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-emerald-600">✓ Todos los productos están sobre el mínimo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
