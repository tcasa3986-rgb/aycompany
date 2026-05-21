@extends('layouts.app')
@section('title', 'Editar Pedido ' . $pedido->numero)

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.show', $pedido) }}">{{ $pedido->numero }}</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-pencil me-2 text-warning"></i>Editar Pedido {{ $pedido->numero }}</h4>
    <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<form method="POST" action="{{ route('pedidos.update', $pedido) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Datos del Pedido</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cliente</label>
                            <input type="text" class="form-control" value="{{ $pedido->cliente->nombre }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado del pedido *</label>
                            <select name="estado" class="form-select" required>
                                @foreach(['pendiente'=>'Pendiente','confirmado'=>'Confirmado','preparando'=>'Preparando','listo'=>'Listo','en_camino'=>'En camino','entregado'=>'Entregado','cancelado'=>'Cancelado','devuelto'=>'Devuelto'] as $val=>$lab)
                                <option value="{{ $val }}" {{ old('estado', $pedido->estado) === $val ? 'selected' : '' }}>{{ $lab }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Repartidor</label>
                            <select name="repartidor_id" class="form-select">
                                <option value="">— Sin asignar —</option>
                                @foreach($repartidores as $rep)
                                <option value="{{ $rep->id }}" {{ old('repartidor_id', $pedido->repartidor_id) == $rep->id ? 'selected' : '' }}>
                                    {{ $rep->nombre }} ({{ ucfirst($rep->estado) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha programada</label>
                            <input type="datetime-local" name="fecha_programada" value="{{ old('fecha_programada', optional($pedido->fecha_programada)->format('Y-m-d\TH:i')) }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas / observaciones</label>
                            <textarea name="notas" rows="3" class="form-control">{{ old('notas', $pedido->notas) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Motivo de cancelación (si aplica)</label>
                            <textarea name="motivo_cancelacion" rows="2" class="form-control">{{ old('motivo_cancelacion', $pedido->motivo_cancelacion) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Resumen</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td>Subtotal</td><td class="text-end">S/ {{ number_format($pedido->subtotal,2) }}</td></tr>
                        <tr><td>Delivery</td><td class="text-end">S/ {{ number_format($pedido->costo_delivery,2) }}</td></tr>
                        <tr><td>Descuento</td><td class="text-end">- S/ {{ number_format($pedido->descuento,2) }}</td></tr>
                        <tr class="fw-bold border-top"><td>Total</td><td class="text-end">S/ {{ number_format($pedido->total,2) }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Guardar cambios</button>
                    <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
