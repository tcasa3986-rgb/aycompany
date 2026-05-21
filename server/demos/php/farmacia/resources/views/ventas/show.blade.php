@extends('layouts.app')
@section('title', 'Venta ' . $venta->codigo)
@section('section', 'Comprobante')

@section('content')
<div class="card card-pad max-w-3xl mx-auto">
    <div class="flex justify-between items-start border-b border-gray-100 pb-4 mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Comprobante {{ strtoupper($venta->tipo_comprobante) }}</h2>
            <p class="text-sm text-gray-500">{{ $venta->codigo }} · {{ $venta->fecha?->format('d/m/Y H:i') }}</p>
        </div>
        <span class="badge {{ $venta->estado === 'emitida' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-600' }} capitalize">
            {{ $venta->estado }}
        </span>
    </div>

    @if($venta->estado !== 'emitida')
    <div class="bg-rose-50 border border-rose-100 rounded-lg p-3 mb-4 text-sm text-rose-800">
        <p class="font-bold">Información de Anulación / Devolución:</p>
        <p><span class="font-semibold">Motivo:</span> {{ $venta->motivo_anulacion }}</p>
        <p><span class="font-semibold">Fecha:</span> {{ $venta->anulada_at?->format('d/m/Y H:i') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
        <div>
            <p class="text-gray-500">Cliente</p>
            <p class="font-medium">{{ $venta->cliente?->nombre_completo ?? 'Cliente genérico' }}</p>
            @if ($venta->cliente?->documento) <p class="text-xs text-gray-500">Doc: {{ $venta->cliente->documento }}</p> @endif
        </div>
        <div>
            <p class="text-gray-500">Cajero</p>
            <p class="font-medium">{{ $venta->cajero?->name }}</p>
            <p class="text-xs text-gray-500">Pago: <span class="capitalize">{{ $venta->forma_pago }}</span></p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Cant.</th>
                    <th class="text-right">P. Unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($venta->detalles as $d)
                    <tr>
                        <td>{{ $d->producto->nombre }}</td>
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
            <div class="flex justify-between"><span class="text-gray-500">Subtotal</span> <span>S/ {{ number_format($venta->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Descuento</span> <span>S/ {{ number_format($venta->descuento, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">IGV</span> <span>S/ {{ number_format($venta->impuesto, 2) }}</span></div>
            <div class="flex justify-between text-lg font-bold text-farmacia-700 mt-2 border-t border-gray-100 pt-2"><span>TOTAL</span> <span>S/ {{ number_format($venta->total, 2) }}</span></div>
            
            <div class="bg-gray-50 p-3 rounded-lg mt-4 space-y-1 border border-gray-100">
                <div class="flex justify-between text-xs text-gray-500 uppercase font-semibold"><span>Metodo de pago</span> <span class="text-gray-700">{{ ucfirst($venta->forma_pago) }}</span></div>
                <div class="flex justify-between text-xs text-gray-500 uppercase font-semibold"><span>Pago recibido</span> <span class="text-gray-700">S/ {{ number_format($venta->pago_recibido, 2) }}</span></div>
                <div class="flex justify-between text-sm font-bold text-emerald-600 uppercase border-t border-gray-200 pt-1 mt-1"><span>Vuelto / Cambio</span> <span>S/ {{ number_format($venta->cambio, 2) }}</span></div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-between gap-2">
        <div class="flex gap-2">
            <a href="{{ route('ventas.index') }}" class="btn-secondary">← Volver</a>
            @if($venta->estado === 'emitida')
                <button type="button" class="btn-danger" onclick="document.getElementById('modal-anular').classList.remove('hidden')">
                    Anular Venta
                </button>
            @endif
        </div>
        <a href="{{ route('pos.index') }}" class="btn-primary">Nueva venta</a>
    </div>
</div>

@if($venta->estado === 'emitida')
<!-- Modal de Anulación -->
<div id="modal-anular" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 animate-in fade-in zoom-in duration-200">
        <h3 class="text-xl font-bold text-gray-800 mb-2">Anular Venta</h3>
        <p class="text-sm text-gray-500 mb-6">Esta acción repondrá el stock de los productos y registrará un egreso en caja si fue pagado en efectivo.</p>
        
        <form action="{{ route('ventas.anular', $venta) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de acción</label>
                <select name="tipo" class="form-control w-full">
                    <option value="anulada">Anulación simple (error de digitación)</option>
                    <option value="devuelta">Devolución de productos</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Motivo</label>
                <textarea name="motivo" class="form-control w-full" rows="3" placeholder="Ej: El cliente se arrepintió, vencido..." required></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" class="btn-secondary" onclick="document.getElementById('modal-anular').classList.add('hidden')">
                    Cancelar
                </button>
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                    Confirmar Anulación
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
