@extends('layouts.app')
@section('title', 'Historial de ventas')
@section('section', 'Ventas')

@section('content')
<div class="card card-pad">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-700">Historial de ventas</h2>
        <a href="{{ route('pos.index') }}" class="btn-primary">
            <x-icon name="cart" class="h-4 w-4 mr-1" /> Nueva venta
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Comprobante</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Cajero</th>
                    <th>Pago</th>
                    <th class="text-right">Total</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($ventas as $v)
                    <tr>
                        <td class="font-mono text-xs text-gray-600">{{ $v->codigo }}</td>
                        <td>{{ $v->fecha?->format('d/m/Y H:i') }}</td>
                        <td>{{ $v->cliente?->nombre_completo ?? 'Cliente genérico' }}</td>
                        <td>{{ $v->cajero?->name }}</td>
                        <td class="capitalize">{{ $v->forma_pago }}</td>
                        <td class="text-right font-semibold">S/ {{ number_format($v->total, 2) }}</td>
                        <td>
                            <span class="badge {{ $v->estado === 'emitida' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-600' }} capitalize">
                                {{ $v->estado }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('ventas.show', $v) }}" class="text-farmacia-600 text-xs hover:underline">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-8 text-center text-gray-400">Aún no hay ventas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $ventas->links() }}</div>
</div>
@endsection
