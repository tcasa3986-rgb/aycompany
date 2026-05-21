@extends('layouts.app')
@section('title', 'Cuentas Corrientes')
@section('section', 'Crédito a Clientes')

@section('content')
<div class="card card-pad">
    <div class="flex justify-between items-center mb-5">
        <h2 class="text-xl font-bold text-gray-700">Estado de Cuentas</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th class="text-right">Límite</th>
                    <th class="text-right">Saldo Deudor</th>
                    <th class="text-right">Disponible</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $c)
                <tr>
                    <td>
                        <div class="font-medium text-gray-800">{{ $c->nombre_completo }}</div>
                        <div class="text-xs text-gray-500">{{ $c->documento }}</div>
                    </td>
                    <td class="text-right">S/ {{ number_format($c->limite_credito, 2) }}</td>
                    <td class="text-right text-rose-600 font-bold">S/ {{ number_format($c->saldo_deudor, 2) }}</td>
                    <td class="text-right text-emerald-600">S/ {{ number_format($c->limite_credito - $c->saldo_deudor, 2) }}</td>
                    <td class="text-center">
                        <a href="{{ route('cuentas.show', $c) }}" class="btn-primary py-1 px-3 text-xs">
                            Ver / Abonar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-400">No hay clientes con crédito configurado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $clientes->links() }}
    </div>
</div>
@endsection
