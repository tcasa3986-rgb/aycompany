@extends('layouts.app')
@section('title', 'Próximos a vencer')
@section('section', 'Reportes')

@section('content')
<div class="card card-pad">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-700">Próximos a vencer</h2>
        <a href="{{ route('reportes.index') }}" class="btn-secondary">← Reportes</a>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div>
            <label class="label">Días a futuro</label>
            <input type="number" min="1" name="dias" value="{{ $dias }}" class="input w-32">
        </div>
        <button type="submit" class="btn-primary">Aplicar</button>
        <a href="{{ route('reportes.vencer', ['dias' => $dias, 'export' => 'pdf']) }}" class="btn-secondary">📄 PDF</a>
        <a href="{{ route('reportes.vencer', ['dias' => $dias, 'export' => 'excel']) }}" class="btn-secondary">📊 Excel</a>
    </form>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Lote</th>
                <th>Vencimiento</th>
                <th class="text-right">Cantidad</th>
                <th>Estado</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($lotes as $l)
                    @php
                        $dr = now()->diffInDays(\Carbon\Carbon::parse($l->fecha_vencimiento), false);
                    @endphp
                    <tr>
                        <td class="font-mono text-xs">{{ $l->codigo }}</td>
                        <td class="font-medium text-gray-800">{{ $l->nombre }}</td>
                        <td>{{ $l->numero_lote }}</td>
                        <td>{{ \Carbon\Carbon::parse($l->fecha_vencimiento)->format('d/m/Y') }}</td>
                        <td class="text-right">{{ $l->cantidad }}</td>
                        <td>
                            @if($dr < 0)
                                <span class="badge bg-rose-50 text-rose-700">Vencido</span>
                            @elseif($dr <= 30)
                                <span class="badge bg-rose-50 text-rose-700">{{ (int)$dr }}d</span>
                            @else
                                <span class="badge bg-amber-50 text-amber-700">{{ (int)$dr }}d</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-emerald-600">✓ No hay lotes próximos a vencer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
