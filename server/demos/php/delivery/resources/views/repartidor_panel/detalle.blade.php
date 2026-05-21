@extends('layouts.app')
@section('title', 'Entrega ' . $entrega->pedido->numero)

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('repartidor.index') }}">Mis entregas</a></li>
        <li class="breadcrumb-item active">{{ $entrega->pedido->numero }}</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title d-inline">{{ $entrega->pedido->numero }}</h4>
        <span class="badge bg-info ms-2 fs-6">{{ ucfirst(str_replace('_',' ', $entrega->estado)) }}</span>
    </div>
    <a href="{{ route('repartidor.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h5>{{ $entrega->pedido->cliente->nombre }}</h5>
        @if($entrega->pedido->cliente->telefono)
        <a href="tel:{{ $entrega->pedido->cliente->telefono }}" class="btn btn-sm btn-success me-2">
            <i class="bi bi-telephone me-1"></i>Llamar
        </a>
        <a href="https://wa.me/51{{ preg_replace('/\D/','',$entrega->pedido->cliente->telefono) }}" target="_blank" class="btn btn-sm btn-success">
            <i class="bi bi-whatsapp me-1"></i>WhatsApp
        </a>
        @endif
        <hr>
        <div><i class="bi bi-geo-alt me-1"></i><strong>Dirección:</strong> {{ $entrega->pedido->direccion_entrega }}</div>
        @if($entrega->pedido->referencia_entrega)<div class="text-muted small">Ref: {{ $entrega->pedido->referencia_entrega }}</div>@endif
        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($entrega->pedido->direccion_entrega) }}" target="_blank" class="btn btn-sm btn-primary mt-2">
            <i class="bi bi-map me-1"></i>Abrir en Google Maps
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Productos a entregar</div>
    <ul class="list-group list-group-flush">
        @foreach($entrega->pedido->items as $i)
        <li class="list-group-item d-flex justify-content-between">
            <div><strong>{{ $i->cantidad }}×</strong> {{ $i->nombre_producto }}
                @if($i->notas)<br><small class="text-muted">{{ $i->notas }}</small>@endif
            </div>
            <span>S/ {{ number_format($i->subtotal, 2) }}</span>
        </li>
        @endforeach
        <li class="list-group-item d-flex justify-content-between fw-bold bg-light">
            <span>TOTAL A COBRAR</span>
            <span class="text-success fs-5">S/ {{ number_format($entrega->pedido->total, 2) }}</span>
        </li>
    </ul>
    <div class="card-footer">
        <strong>Pago:</strong> {{ ucfirst($entrega->pedido->tipo_pago) }} —
        Estado: <span class="badge bg-{{ $entrega->pedido->estado_pago === 'pagado' ? 'success' : 'warning' }}">{{ ucfirst($entrega->pedido->estado_pago) }}</span>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-arrow-repeat me-2"></i>Actualizar estado</div>
    <div class="card-body">
        <form method="POST" action="{{ route('repartidor.actualizar', $entrega) }}" id="formEstado">
            @csrf
            <input type="hidden" name="lat" id="latInput">
            <input type="hidden" name="lng" id="lngInput">

            <div class="d-grid gap-2">
                @if($entrega->estado === 'asignado')
                <button type="submit" name="estado" value="recogido" class="btn btn-info btn-lg"><i class="bi bi-bag-check me-1"></i>Pedido recogido del local</button>
                @endif
                @if(in_array($entrega->estado, ['asignado','recogido']))
                <button type="submit" name="estado" value="en_camino" class="btn btn-primary btn-lg"><i class="bi bi-bicycle me-1"></i>Salí en camino</button>
                @endif
                @if(in_array($entrega->estado, ['asignado','recogido','en_camino']))
                <button type="submit" name="estado" value="entregado" class="btn btn-success btn-lg" onclick="return capturarUbicacion()"><i class="bi bi-check-circle me-1"></i>Entregado al cliente</button>
                <button type="submit" name="estado" value="fallido" class="btn btn-outline-danger" onclick="return confirm('¿Reportar entrega fallida?')"><i class="bi bi-x-circle me-1"></i>No se pudo entregar</button>
                @endif
            </div>

            <div class="mt-3">
                <label class="form-label small fw-semibold">Observaciones (opcional)</label>
                <textarea name="observaciones" class="form-control" rows="2" placeholder="Comentarios para registro interno"></textarea>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function capturarUbicacion() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(p => {
            document.getElementById('latInput').value = p.coords.latitude;
            document.getElementById('lngInput').value = p.coords.longitude;
        });
    }
    return true;
}
</script>
@endpush
@endsection
