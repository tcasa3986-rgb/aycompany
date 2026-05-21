@extends('layouts.app')
@section('title', 'Repartidor: ' . $repartidor->nombre_completo)

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('repartidores.index') }}">Repartidores</a></li>
        <li class="breadcrumb-item active">{{ $repartidor->nombre_completo }}</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title">{{ $repartidor->nombre_completo }}</h4>
    @can('editar repartidores')
    <a href="{{ route('repartidores.edit', $repartidor) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Editar</a>
    @endcan
</div>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body py-4">
                <img src="{{ $repartidor->foto_url }}" class="rounded-circle mb-3" width="90" height="90" alt="">
                <h5 class="fw-bold">{{ $repartidor->nombre_completo }}</h5>
                <span class="badge bg-{{ ['disponible'=>'success','ocupado'=>'warning','descanso'=>'info','inactivo'=>'secondary'][$repartidor->estado] ?? 'secondary' }} fs-6">
                    {{ ucfirst($repartidor->estado) }}
                </span>
                <div class="text-start mt-3">
                    <div class="mb-2 small"><i class="bi bi-card-text me-2 text-muted"></i>DNI: {{ $repartidor->dni }}</div>
                    <div class="mb-2 small"><i class="bi bi-telephone me-2 text-muted"></i>{{ $repartidor->telefono }}</div>
                    @if($repartidor->email)<div class="mb-2 small"><i class="bi bi-envelope me-2 text-muted"></i>{{ $repartidor->email }}</div>@endif
                    <div class="mb-2 small"><i class="{{ $repartidor->vehiculo_icono }} me-2 text-muted"></i>{{ ucfirst($repartidor->tipo_vehiculo) }}{{ $repartidor->placa ? ' · ' . $repartidor->placa : '' }}</div>
                    @if($repartidor->zona_asignada)<div class="mb-2 small"><i class="bi bi-map me-2 text-muted"></i>{{ $repartidor->zona_asignada }}</div>@endif
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">Estadísticas del Mes</div>
            <div class="card-body">
                <div class="row text-center g-2">
                    <div class="col-6"><div class="fw-bold fs-4 text-success">{{ $estadisticas['entregas_mes'] }}</div><small class="text-muted">Entregas</small></div>
                    <div class="col-6"><div class="fw-bold fs-4 text-primary">⭐ {{ number_format($estadisticas['calificacion_prom'] ?? 0, 1) }}</div><small class="text-muted">Calificación</small></div>
                    <div class="col-6"><div class="fw-bold fs-4 text-warning">{{ $estadisticas['total_entregas'] }}</div><small class="text-muted">Total Entregas</small></div>
                    <div class="col-6"><div class="fw-bold fs-4 text-info">{{ round($estadisticas['tiempo_promedio'] ?? 0) }} min</div><small class="text-muted">Tiempo Prom.</small></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Últimas Entregas</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th class="ps-3">Pedido</th><th>Cliente</th><th>Estado</th><th>Fecha</th><th>Tiempo</th><th>Calif.</th></tr></thead>
                    <tbody>
                    @forelse($entregas as $e)
                    <tr>
                        <td class="ps-3"><a href="{{ route('pedidos.show', $e->pedido) }}" class="text-decoration-none">{{ $e->pedido?->numero }}</a></td>
                        <td class="small">{{ $e->pedido?->cliente?->nombre_completo }}</td>
                        <td><span class="badge bg-{{ $e->estado_badge }}">{{ $e->estado }}</span></td>
                        <td class="small text-muted">{{ $e->fecha_asignacion->format('d/m H:i') }}</td>
                        <td class="small">{{ $e->tiempo_minutos ? $e->tiempo_minutos . ' min' : '—' }}</td>
                        <td class="small">{{ $e->calificacion ? '⭐ ' . $e->calificacion : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Sin entregas registradas</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
