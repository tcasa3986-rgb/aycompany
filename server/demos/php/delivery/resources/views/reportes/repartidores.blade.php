@extends('layouts.app')
@section('title', 'Reporte de Repartidores')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li class="breadcrumb-item active">Repartidores</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-bicycle me-2 text-success"></i>Rendimiento de Repartidores</h4>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label small fw-semibold">Desde</label>
                <input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-semibold">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}" class="form-control"></div>
            <div class="col-auto d-flex align-items-end">
                <button type="submit" class="btn btn-success"><i class="bi bi-search me-1"></i>Generar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Período: {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Repartidor</th>
                        <th>Vehículo</th>
                        <th>Zona</th>
                        <th class="text-center">Entregas</th>
                        <th class="text-center">Tiempo Prom.</th>
                        <th class="text-center">Calificación</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($repartidores as $i => $rep)
                <tr>
                    <td class="ps-3">
                        @if($i < 3)
                        <span class="badge bg-{{ ['warning','secondary','dark'][$i] }}">{{ $i+1 }}</span>
                        @else
                        <span class="text-muted">{{ $i+1 }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $rep->foto_url }}" class="avatar-sm" alt="">
                            <div>
                                <div class="fw-semibold small">{{ $rep->nombre_completo }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $rep->telefono }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="small"><i class="{{ $rep->vehiculo_icono }} me-1"></i>{{ ucfirst($rep->tipo_vehiculo) }}</td>
                    <td class="small">{{ $rep->zona_asignada ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-success fs-6">{{ $rep->entregas_periodo }}</span>
                    </td>
                    <td class="text-center small">
                        {{ $rep->tiempo_promedio ? round($rep->tiempo_promedio) . ' min' : '—' }}
                    </td>
                    <td class="text-center">
                        @if($rep->calificacion_prom)
                        <span class="text-warning">⭐</span> {{ number_format($rep->calificacion_prom, 1) }}
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td><span class="badge bg-{{ ['disponible'=>'success','ocupado'=>'warning','inactivo'=>'secondary','descanso'=>'info'][$rep->estado] ?? 'secondary' }}">{{ ucfirst($rep->estado) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Sin datos en este período</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
