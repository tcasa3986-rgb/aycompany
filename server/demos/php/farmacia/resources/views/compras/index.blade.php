@extends('layouts.app')
@section('title', 'Compras')
@section('section', 'Órdenes de compra')

@section('content')
<div class="card card-pad">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Órdenes de compra</h2>
            <p class="text-sm text-gray-500">Gestión de compras a proveedores y recepción de mercadería.</p>
        </div>
        <div class="flex gap-2">
            <form method="GET">
                <select name="estado" class="input w-44" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    @foreach (['pendiente','parcial','recibida','anulada'] as $e)
                        <option value="{{ $e }}" @selected($estado === $e)>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('compras.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4 mr-1" /> Nueva orden
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>Código</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th class="text-right">Total</th>
                <th>Estado</th>
                <th></th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($compras as $c)
                    <tr>
                        <td class="font-mono text-xs text-gray-500">{{ $c->codigo }}</td>
                        <td>{{ $c->fecha?->format('d/m/Y') }}</td>
                        <td class="font-medium text-gray-800">{{ $c->proveedor?->razon_social }}</td>
                        <td class="text-right font-semibold">S/ {{ number_format($c->total, 2) }}</td>
                        <td>
                            @php $colors = ['pendiente' => 'amber', 'parcial' => 'sky', 'recibida' => 'emerald', 'anulada' => 'rose']; @endphp
                            <span class="badge bg-{{ $colors[$c->estado] }}-50 text-{{ $colors[$c->estado] }}-700 capitalize">{{ $c->estado }}</span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('compras.show', $c) }}" class="text-farmacia-600 text-xs hover:underline">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Sin órdenes registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $compras->links() }}</div>
</div>
@endsection
