@extends('layouts.app')
@section('title', 'Editar Repartidor')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('repartidores.index') }}">Repartidores</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-pencil me-2 text-warning"></i>Editar Repartidor</h4>
    <a href="{{ route('repartidores.show', $repartidor) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>
<form method="POST" action="{{ route('repartidores.update', $repartidor) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Datos</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Nombre *</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $repartidor->nombre) }}" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Apellido *</label>
                            <input type="text" name="apellido" value="{{ old('apellido', $repartidor->apellido) }}" class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">DNI *</label>
                            <input type="text" name="dni" value="{{ old('dni', $repartidor->dni) }}" class="form-control @error('dni') is-invalid @enderror" required>
                            @error('dni')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Teléfono *</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $repartidor->telefono) }}" class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Estado</label>
                            <select name="estado" class="form-select">
                                @foreach(['disponible','ocupado','descanso','inactivo'] as $e)
                                <option value="{{ $e }}" {{ old('estado', $repartidor->estado)===$e?'selected':'' }}>{{ ucfirst($e) }}</option>
                                @endforeach
                            </select></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Vehículo *</label>
                            <select name="tipo_vehiculo" class="form-select" required>
                                @foreach(['moto','bicicleta','auto','pie'] as $v)
                                <option value="{{ $v }}" {{ old('tipo_vehiculo', $repartidor->tipo_vehiculo)===$v?'selected':'' }}>{{ ucfirst($v) }}</option>
                                @endforeach
                            </select></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Placa</label>
                            <input type="text" name="placa" value="{{ old('placa', $repartidor->placa) }}" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">Zona Asignada</label>
                            <input type="text" name="zona_asignada" value="{{ old('zona_asignada', $repartidor->zona_asignada) }}" class="form-control"></div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ $repartidor->activo ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">Repartidor activo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            @if($repartidor->foto)
            <div class="card mb-3 text-center"><div class="card-body">
                <img src="{{ $repartidor->foto_url }}" class="rounded-circle" width="80" height="80" alt="">
                <div class="small text-muted mt-2">Foto actual</div>
            </div></div>
            @endif
            <div class="card mb-3"><div class="card-header">Nueva Foto</div>
                <div class="card-body"><input type="file" name="foto" class="form-control" accept="image/*"></div>
            </div>
            <div class="card"><div class="card-body">
                <button type="submit" class="btn btn-warning w-100"><i class="bi bi-check-lg me-1"></i>Actualizar</button>
                <a href="{{ route('repartidores.show', $repartidor) }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
            </div></div>
        </div>
    </div>
</form>
@endsection
