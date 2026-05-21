@extends('layouts.app')
@section('title', 'Lotes · ' . $producto->nombre)
@section('section', 'Inventario')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <div class="card card-pad lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Lotes de {{ $producto->nombre }}</h2>
                <p class="text-sm text-gray-500">
                    Stock actual del producto: <span class="font-semibold text-farmacia-700">{{ $producto->stock }}</span>
                    · Total en lotes: <span class="font-semibold">{{ $totalEnLotes }}</span>
                </p>
            </div>
            <a href="{{ route('productos.index') }}" class="btn-secondary">← Productos</a>
        </div>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr>
                    <th>Número de lote</th>
                    <th>Vencimiento</th>
                    <th class="text-right">Cantidad</th>
                    <th>Estado</th>
                    <th></th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($lotes as $lote)
                        @php
                            $diasRestantes = now()->diffInDays(\Carbon\Carbon::parse($lote->fecha_vencimiento), false);
                            $estado = $diasRestantes < 0 ? 'vencido' : ($diasRestantes <= 90 ? 'por_vencer' : 'vigente');
                        @endphp
                        <tr>
                            <td class="font-mono text-xs">{{ $lote->numero_lote }}</td>
                            <td>{{ \Carbon\Carbon::parse($lote->fecha_vencimiento)->format('d/m/Y') }}</td>
                            <td class="text-right">{{ $lote->cantidad }}</td>
                            <td>
                                @if($estado === 'vencido')
                                    <span class="badge bg-rose-50 text-rose-700">Vencido</span>
                                @elseif($estado === 'por_vencer')
                                    <span class="badge bg-amber-50 text-amber-700">Por vencer ({{ (int) $diasRestantes }}d)</span>
                                @else
                                    <span class="badge bg-emerald-50 text-emerald-700">Vigente</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('productos.lotes.destroy', [$producto, $lote]) }}"
                                      onsubmit="return confirm('¿Eliminar este lote?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded hover:bg-gray-100">
                                        <x-icon name="trash" class="h-4 w-4 text-rose-500" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-400">Aún no hay lotes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-pad">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">Agregar lote</h2>
        <form method="POST" action="{{ route('productos.lotes.store', $producto) }}" class="space-y-3">
            @csrf
            <div>
                <label class="label">Número de lote *</label>
                <input type="text" name="numero_lote" class="input" required>
            </div>
            <div>
                <label class="label">Fecha de vencimiento *</label>
                <input type="date" name="fecha_vencimiento" class="input" required>
            </div>
            <div>
                <label class="label">Cantidad *</label>
                <input type="number" name="cantidad" min="1" value="1" class="input" required>
            </div>
            <label class="inline-flex items-center text-sm text-gray-700">
                <input type="checkbox" name="sumar_stock" value="1" checked class="rounded text-farmacia-500 focus:ring-farmacia-400">
                <span class="ml-2">Sumar al stock del producto</span>
            </label>
            <button type="submit" class="btn-primary w-full">Guardar lote</button>
        </form>
    </div>
</div>
@endsection
