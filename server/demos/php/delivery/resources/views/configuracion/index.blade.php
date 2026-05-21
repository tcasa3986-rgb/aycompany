@extends('layouts.app')
@section('title', 'Configuración')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Configuración</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-gear me-2 text-primary"></i>Configuración del Sistema</h4>
</div>

<form method="POST" action="{{ route('configuracion.update') }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <!-- Empresa -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-building me-2 text-primary"></i>Datos de la Empresa</div>
                <div class="card-body">
                    @foreach($configs->get('empresa', collect()) as $config)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ ucfirst(str_replace(['empresa_','_'], ['','  '], $config->clave)) }}</label>
                        <input type="text" name="{{ $config->clave }}" value="{{ old($config->clave, $config->valor) }}" class="form-control">
                        @if($config->descripcion)<div class="form-text">{{ $config->descripcion }}</div>@endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Delivery -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-truck me-2 text-success"></i>Configuración de Delivery</div>
                <div class="card-body">
                    @foreach($configs->get('delivery', collect()) as $config)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ ucfirst(str_replace(['delivery_','_'], ['','  '], $config->clave)) }}</label>
                        <input type="{{ $config->tipo === 'number' ? 'number' : 'text' }}"
                               step="{{ $config->tipo === 'number' ? '0.50' : null }}"
                               name="{{ $config->clave }}" value="{{ old($config->clave, $config->valor) }}" class="form-control">
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><i class="bi bi-sliders me-2 text-info"></i>Sistema</div>
                <div class="card-body">
                    @foreach($configs->get('sistema', collect()) as $config)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ ucfirst(str_replace('_', ' ', $config->clave)) }}</label>
                        <input type="{{ $config->tipo === 'number' ? 'number' : 'text' }}"
                               name="{{ $config->clave }}" value="{{ old($config->clave, $config->valor) }}" class="form-control">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>Guardar Configuración
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
