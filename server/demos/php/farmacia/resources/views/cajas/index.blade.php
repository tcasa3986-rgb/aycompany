@extends('layouts.app')
@section('title', 'Caja')
@section('section', 'Caja registradora')

@section('content')

@if (! $cajaAbierta)
    <div class="card card-pad mb-5 max-w-xl">
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Abrir caja</h2>
        <p class="text-sm text-gray-500 mb-3">Registra el monto inicial para iniciar el turno.</p>

        <form method="POST" action="{{ route('cajas.abrir') }}" class="space-y-3">
            @csrf
            <div>
                <label class="label">Monto de apertura (S/) *</label>
                <input type="number" step="0.01" min="0" name="monto_apertura" value="0" class="input" required>
            </div>
            <div>
                <label class="label">Observaciones</label>
                <textarea name="observaciones" rows="2" class="input"></textarea>
            </div>
            <button type="submit" class="btn-primary">Abrir caja</button>
        </form>
    </div>
@else
    <div class="card card-pad mb-5 bg-farmacia-50 border-farmacia-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-farmacia-700 font-semibold uppercase tracking-wider">Caja abierta</p>
                <p class="text-gray-700 mt-1">Apertura: {{ $cajaAbierta->apertura?->format('d/m/Y H:i') }} · Monto inicial S/ {{ number_format($cajaAbierta->monto_apertura, 2) }}</p>
            </div>
            <a href="{{ route('cajas.show', $cajaAbierta) }}" class="btn-primary">Ir a la caja</a>
        </div>
    </div>
@endif

<div class="card card-pad">
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Historial de cajas</h2>
    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>Cajero</th>
                <th>Apertura</th>
                <th>Cierre</th>
                <th class="text-right">Apertura S/</th>
                <th class="text-right">Ventas S/</th>
                <th class="text-right">Cierre S/</th>
                <th>Estado</th>
                <th></th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($cajas as $c)
                    <tr>
                        <td class="font-medium text-gray-800">{{ $c->usuario?->name }}</td>
                        <td>{{ $c->apertura?->format('d/m H:i') }}</td>
                        <td>{{ $c->cierre?->format('d/m H:i') ?? '—' }}</td>
                        <td class="text-right">{{ number_format($c->monto_apertura, 2) }}</td>
                        <td class="text-right">{{ number_format($c->total_ventas, 2) }}</td>
                        <td class="text-right">{{ $c->monto_cierre !== null ? number_format($c->monto_cierre, 2) : '—' }}</td>
                        <td>
                            <span class="badge {{ $c->estado === 'abierta' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-700' }} capitalize">
                                {{ $c->estado }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('cajas.show', $c) }}" class="text-farmacia-600 text-xs hover:underline">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-8 text-center text-gray-400">Sin cajas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $cajas->links() }}</div>
</div>
@endsection
