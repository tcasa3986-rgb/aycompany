@extends('layouts.app')
@section('title', 'Panel del Repartidor')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item active">Mis entregas</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-bicycle me-2 text-primary"></i>Hola, {{ $rep->nombre }}</h4>
    <div>
        <span class="badge bg-{{ $rep->estado === 'disponible' ? 'success' : ($rep->estado === 'ocupado' ? 'warning' : 'secondary') }} fs-6">
            {{ ucfirst($rep->estado) }}
        </span>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card text-center"><div class="card-body">
            <i class="bi bi-truck text-primary fs-2"></i>
            <div class="text-muted small">Activas</div>
            <h4 class="mb-0">{{ $entregas->count() }}</h4>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center"><div class="card-body">
            <i class="bi bi-check-circle text-success fs-2"></i>
            <div class="text-muted small">Entregadas hoy</div>
            <h4 class="mb-0">{{ $hoy }}</h4>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-list-check me-2"></i>Mis pedidos asignados</div>
    <div class="list-group list-group-flush">
        @forelse($entregas as $e)
        <a href="{{ route('repartidor.entrega', $e) }}" class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong>{{ $e->pedido->numero }}</strong>
                    <span class="badge bg-{{ $e->estado_badge ?? 'info' }} ms-2">{{ ucfirst(str_replace('_',' ', $e->estado)) }}</span>
                    <div class="mt-1">
                        <i class="bi bi-person me-1"></i>{{ $e->pedido->cliente->nombre }}
                        @if($e->pedido->cliente->telefono)
                            <a href="tel:{{ $e->pedido->cliente->telefono }}" class="ms-2"><i class="bi bi-telephone"></i> {{ $e->pedido->cliente->telefono }}</a>
                        @endif
                    </div>
                    <div class="text-muted small mt-1"><i class="bi bi-geo-alt"></i> {{ $e->pedido->direccion_entrega }}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-success">S/ {{ number_format($e->pedido->total, 2) }}</div>
                    <small class="text-muted">{{ ucfirst($e->pedido->tipo_pago) }}</small>
                </div>
            </div>
        </a>
        @empty
        <div class="list-group-item text-center text-muted py-4">
            <i class="bi bi-emoji-smile fs-2 d-block mb-2"></i>
            No tienes entregas asignadas en este momento.
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
// Reportar ubicación GPS al servidor cada 60s mientras el panel esté abierto
function enviarUbicacion(pos) {
    fetch('{{ route("repartidor.ubicacion") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({lat: pos.coords.latitude, lng: pos.coords.longitude})
    }).catch(()=>{});
}
function rastrear() {
    if (!navigator.geolocation) return;
    navigator.geolocation.getCurrentPosition(enviarUbicacion);
}
rastrear();
setInterval(rastrear, 60000);
</script>
@endpush
@endsection
