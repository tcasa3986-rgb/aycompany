@extends('layouts.app')
@section('title', 'Repartidores')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Repartidores</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-bicycle me-2 text-primary"></i>Repartidores</h4>
    @can('crear repartidores')
    <a href="{{ route('repartidores.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo Repartidor</a>
    @endcan
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Nombre, DNI, teléfono...">
                </div>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="disponible" {{ request('estado')==='disponible'?'selected':'' }}>Disponible</option>
                    <option value="ocupado" {{ request('estado')==='ocupado'?'selected':'' }}>Ocupado</option>
                    <option value="descanso" {{ request('estado')==='descanso'?'selected':'' }}>Descanso</option>
                    <option value="inactivo" {{ request('estado')==='inactivo'?'selected':'' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tipo_vehiculo" class="form-select">
                    <option value="">Vehículo</option>
                    @foreach(['moto','bicicleta','auto','pie'] as $v)
                    <option value="{{ $v }}" {{ request('tipo_vehiculo')===$v?'selected':'' }}>{{ ucfirst($v) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="{{ route('repartidores.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    @forelse($repartidores as $rep)
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <img src="{{ $rep->foto_url }}" class="avatar-md" alt="">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-0">{{ $rep->nombre_completo }}</h6>
                            <span class="badge bg-{{ ['disponible'=>'success','ocupado'=>'warning','descanso'=>'info','inactivo'=>'secondary'][$rep->estado] ?? 'secondary' }}">
                                {{ ucfirst($rep->estado) }}
                            </span>
                        </div>
                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $rep->telefono }}</div>
                        <div class="text-muted small"><i class="{{ $rep->vehiculo_icono }} me-1"></i>{{ ucfirst($rep->tipo_vehiculo) }}{{ $rep->placa ? ' · ' . $rep->placa : '' }}</div>
                        @if($rep->zona_asignada)
                        <div class="text-muted small"><i class="bi bi-map me-1"></i>{{ $rep->zona_asignada }}</div>
                        @endif
                    </div>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between text-center small">
                    <div>
                        <div class="fw-bold">{{ $rep->entregas_count }}</div>
                        <div class="text-muted">Entregas</div>
                    </div>
                    <div>
                        <div class="fw-bold">⭐ {{ number_format($rep->calificacion, 1) }}</div>
                        <div class="text-muted">Calificación</div>
                    </div>
                    <div>
                        <div class="fw-bold">DNI {{ $rep->dni }}</div>
                        <div class="text-muted">Documento</div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('repartidores.show', $rep) }}" class="btn btn-sm btn-outline-info flex-fill"><i class="bi bi-eye me-1"></i>Ver</a>
                    @can('editar repartidores')
                    <a href="{{ route('repartidores.edit', $rep) }}" class="btn btn-sm btn-outline-warning flex-fill"><i class="bi bi-pencil me-1"></i>Editar</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card text-center py-5">
            <div class="text-muted"><i class="bi bi-bicycle display-4 d-block mb-3"></i>No se encontraron repartidores</div>
        </div>
    </div>
    @endforelse
</div>

@if($repartidores->hasPages())
<div class="d-flex justify-content-center mt-3">{{ $repartidores->links() }}</div>
@endif
@endsection
