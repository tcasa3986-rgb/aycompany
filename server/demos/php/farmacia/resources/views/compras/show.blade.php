@extends('layouts.app')
@section('title', 'OC ' . $compra->codigo)
@section('section', 'Compra')

@section('content')
<div class="card card-pad max-w-4xl mx-auto">
    <div class="flex justify-between items-start border-b border-gray-100 pb-4 mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Orden de Compra</h2>
            <p class="text-sm text-gray-500">{{ $compra->codigo }} · {{ $compra->fecha?->format('d/m/Y') }}</p>
        </div>
        @php $colors = ['pendiente' => 'amber', 'parcial' => 'sky', 'recibida' => 'emerald', 'anulada' => 'rose']; @endphp
        <span class="badge bg-{{ $colors[$compra->estado] }}-50 text-{{ $colors[$compra->estado] }}-700 capitalize">
            {{ $compra->estado }}
        </span>
    </div>

    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
        <div>
            <p class="text-gray-500">Proveedor</p>
            <p class="font-medium">{{ $compra->proveedor?->razon_social }}</p>
            @if($compra->proveedor?->ruc) <p class="text-xs text-gray-500">RUC: {{ $compra->proveedor->ruc }}</p> @endif
        </div>
        <div>
            <p class="text-gray-500">Fecha de recepción</p>
            <p class="font-medium">{{ $compra->fecha_recepcion?->format('d/m/Y') ?? '—' }}</p>
            @if($compra->observaciones)
                <p class="text-xs text-gray-500 mt-1">Obs: {{ $compra->observaciones }}</p>
            @endif
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>Producto</th>
                <th>Lote</th>
                <th>Vence</th>
                <th class="text-right">Cant.</th>
                <th class="text-right">P. Unit.</th>
                <th class="text-right">Subtotal</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($compra->detalles as $d)
                    <tr>
                        <td class="font-medium text-gray-800">{{ $d->producto->nombre }}</td>
                        <td>{{ $d->numero_lote ?? '—' }}</td>
                        <td>{{ $d->fecha_vencimiento ? \Carbon\Carbon::parse($d->fecha_vencimiento)->format('d/m/Y') : '—' }}</td>
                        <td class="text-right">{{ $d->cantidad }}</td>
                        <td class="text-right">S/ {{ number_format($d->precio_unitario, 2) }}</td>
                        <td class="text-right">S/ {{ number_format($d->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-end">
        <div class="w-72 space-y-1 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Subtotal</span> <span>S/ {{ number_format($compra->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">IGV</span> <span>S/ {{ number_format($compra->impuesto, 2) }}</span></div>
            <div class="flex justify-between text-lg font-bold text-farmacia-700 mt-2"><span>TOTAL</span> <span>S/ {{ number_format($compra->total, 2) }}</span></div>
        </div>
    </div>

    <div class="mt-6 flex justify-between">
        <a href="{{ route('compras.index') }}" class="btn-secondary">← Volver</a>
        <div class="flex gap-2">
            @if (! in_array($compra->estado, ['recibida', 'anulada']))
                <form method="POST" action="{{ route('compras.anular', $compra) }}" onsubmit="return confirm('¿Anular esta orden?')">
                    @csrf
                    <button type="submit" class="btn-danger">Anular</button>
                </form>
                <form method="POST" action="{{ route('compras.recibir', $compra) }}"
                      onsubmit="return confirm('Confirmar recepción de mercadería. Se incrementará el stock.')">
                    @csrf
                    <button type="submit" class="btn-primary">Recibir mercadería</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
