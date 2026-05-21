@extends('layouts.app')
@section('title', 'Cuenta de ' . $cliente->nombre_completo)

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-1">
        <div class="card card-pad bg-white shadow-sm border-0">
            <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Información del Cliente</h3>
            <div class="space-y-3 text-sm">
                <div><p class="text-gray-500">Nombre</p><p class="font-medium">{{ $cliente->nombre_completo }}</p></div>
                <div><p class="text-gray-500">Documento</p><p class="font-medium">{{ $cliente->documento }}</p></div>
                <div><p class="text-gray-500">Límite de Crédito</p><p class="font-medium text-emerald-600">S/ {{ number_format($cliente->limite_credito, 2) }}</p></div>
                <div><p class="text-gray-500">Deuda Actual</p><p class="text-xl font-bold text-rose-600">S/ {{ number_format($cliente->saldo_deudor, 2) }}</p></div>
            </div>

            @if($cliente->saldo_deudor > 0)
            <div class="mt-8 border-t pt-4">
                <h4 class="font-bold text-gray-700 mb-3">Registrar Abono</h4>
                <form action="{{ route('cuentas.abono', $cliente) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="label">Monto a abonar</label>
                        <input type="number" name="monto" step="0.01" max="{{ $cliente->saldo_deudor }}" class="input" required>
                    </div>
                    <div class="mb-4">
                        <label class="label">Forma de pago</label>
                        <select name="forma_pago" class="input">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary w-full">Procesar Pago</button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div class="md:col-span-2">
        <div class="card card-pad bg-white shadow-sm border-0">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Ventas al Crédito Pendientes</h3>
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Fecha</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventasPendientes as $v)
                        <tr>
                            <td class="font-medium">{{ $v->codigo }}</td>
                            <td>{{ $v->fecha->format('d/m/Y') }}</td>
                            <td class="text-right font-bold">S/ {{ number_format($v->total, 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('ventas.show', $v) }}" target="_blank" class="text-farmacia-600 hover:underline">Detalle</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-400">No hay ventas pendientes de pago.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
