@extends('layouts.app')
@section('title', 'Caja #' . $caja->id)
@section('section', 'Caja')

@section('content')
@php
    $ingresos = $caja->movimientos->where('tipo', 'ingreso')->sum('monto');
    $egresos  = $caja->movimientos->where('tipo', 'egreso')->sum('monto');
    $totalVentas = $caja->estado === 'cerrada'
        ? $caja->total_ventas
        : $ventas->sum('total');
    $esperado = $caja->monto_apertura + $totalVentas + $ingresos - $egresos;
    $diferencia = $caja->monto_cierre !== null ? ($caja->monto_cierre - $esperado) : null;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Resumen --}}
    <div class="card card-pad lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Caja de {{ $caja->usuario?->name }}</h2>
                <p class="text-sm text-gray-500">Apertura: {{ $caja->apertura?->format('d/m/Y H:i') }}
                    @if($caja->cierre) · Cierre: {{ $caja->cierre->format('d/m/Y H:i') }} @endif
                </p>
            </div>
            <span class="badge {{ $caja->estado === 'abierta' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-700' }} capitalize">
                {{ $caja->estado }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="rounded-xl bg-farmacia-50 p-4">
                <p class="text-xs uppercase text-farmacia-700 font-semibold tracking-wider">Apertura</p>
                <p class="text-xl font-bold text-farmacia-700 mt-1">S/ {{ number_format($caja->monto_apertura, 2) }}</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-4">
                <p class="text-xs uppercase text-emerald-700 font-semibold tracking-wider">Ventas</p>
                <p class="text-xl font-bold text-emerald-700 mt-1">S/ {{ number_format($totalVentas, 2) }}</p>
            </div>
            <div class="rounded-xl bg-sky-50 p-4">
                <p class="text-xs uppercase text-sky-700 font-semibold tracking-wider">Ingresos</p>
                <p class="text-xl font-bold text-sky-700 mt-1">S/ {{ number_format($ingresos, 2) }}</p>
            </div>
            <div class="rounded-xl bg-rose-50 p-4">
                <p class="text-xs uppercase text-rose-700 font-semibold tracking-wider">Egresos</p>
                <p class="text-xl font-bold text-rose-700 mt-1">S/ {{ number_format($egresos, 2) }}</p>
            </div>
        </div>

        <div class="mt-5 border-t border-gray-100 pt-4 flex items-center justify-between">
            <p class="text-gray-600 text-sm">Esperado en caja</p>
            <p class="text-2xl font-bold text-farmacia-700">S/ {{ number_format($esperado, 2) }}</p>
        </div>
        @if ($diferencia !== null)
            <p class="text-right text-sm mt-1
                {{ $diferencia == 0 ? 'text-emerald-600' : ($diferencia > 0 ? 'text-amber-600' : 'text-rose-600') }}">
                Diferencia con cierre: S/ {{ number_format($diferencia, 2) }}
                @if($diferencia == 0) (cuadrado) @elseif($diferencia > 0) (sobrante) @else (faltante) @endif
            </p>
        @endif

        <h3 class="mt-6 mb-2 text-sm font-semibold text-gray-700 uppercase tracking-wider">Movimientos manuales</h3>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Hora</th><th>Tipo</th><th>Concepto</th><th class="text-right">Monto</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($caja->movimientos->sortByDesc('created_at') as $m)
                        <tr>
                            <td class="text-xs text-gray-500">{{ $m->created_at->format('H:i') }}</td>
                            <td>
                                <span class="badge {{ $m->tipo === 'ingreso' ? 'bg-sky-50 text-sky-700' : 'bg-rose-50 text-rose-700' }} capitalize">
                                    {{ $m->tipo }}
                                </span>
                            </td>
                            <td>{{ $m->concepto }}</td>
                            <td class="text-right font-semibold">S/ {{ number_format($m->monto, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-400">Sin movimientos manuales.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h3 class="mt-6 mb-2 text-sm font-semibold text-gray-700 uppercase tracking-wider">Ventas en este turno</h3>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Comprobante</th><th>Hora</th><th>Cliente</th><th class="text-right">Total</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($ventas as $v)
                        <tr>
                            <td class="font-mono text-xs">{{ $v->codigo }}</td>
                            <td class="text-xs">{{ $v->fecha?->format('H:i') }}</td>
                            <td>{{ $v->cliente?->nombre_completo ?? 'Genérico' }}</td>
                            <td class="text-right font-semibold">S/ {{ number_format($v->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-400">Sin ventas en este turno.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="space-y-5">
        @if ($caja->estado === 'abierta')
            <div class="card card-pad">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Movimiento manual</h2>
                <form method="POST" action="{{ route('cajas.movimiento', $caja) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="label">Tipo</label>
                        <select name="tipo" class="input">
                            <option value="ingreso">Ingreso</option>
                            <option value="egreso">Egreso</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Monto S/</label>
                        <input type="number" step="0.01" min="0.01" name="monto" class="input" required>
                    </div>
                    <div>
                        <label class="label">Concepto</label>
                        <input type="text" name="concepto" class="input" required>
                    </div>
                    <button type="submit" class="btn-secondary w-full">Registrar movimiento</button>
                </form>
            </div>

            <div class="card card-pad">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Cerrar caja</h2>
                <form method="POST" action="{{ route('cajas.cerrar', $caja) }}" class="space-y-3"
                      onsubmit="return confirm('¿Cerrar la caja? Esta acción no se puede deshacer.')">
                    @csrf
                    <p class="text-xs text-gray-500">Esperado en caja: <span class="font-semibold">S/ {{ number_format($esperado, 2) }}</span></p>
                    <div>
                        <label class="label">Monto contado en cierre S/ *</label>
                        <input type="number" step="0.01" min="0" name="monto_cierre" class="input" required>
                    </div>
                    <div>
                        <label class="label">Observaciones</label>
                        <textarea name="observaciones" rows="2" class="input"></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full">Cerrar caja</button>
                </form>
            </div>
        @else
            <div class="card card-pad">
                <p class="text-sm text-gray-500">Esta caja está cerrada. No se permiten más movimientos.</p>
                <a href="{{ route('cajas.index') }}" class="btn-secondary mt-3 w-full text-center">← Historial</a>
            </div>
        @endif
    </div>
</div>
@endsection
