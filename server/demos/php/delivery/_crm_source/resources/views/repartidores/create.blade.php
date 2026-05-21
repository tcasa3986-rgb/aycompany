@extends('layouts.app')
@section('title', 'Nuevo Repartidor')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('repartidores.index') }}">Repartidores</a></li>
        <li class="breadcrumb-item active">Nuevo</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-person-plus me-2 text-primary"></i>Nuevo Repartidor</h4>
    <a href="{{ route('repartidores.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<form method="POST" action="{{ route('repartidores.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Datos Personales</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido *</label>
                            <input type="text" name="apellido" value="{{ old('apellido') }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">DNI *</label>
                            <input type="text" name="dni" value="{{ old('dni') }}" class="form-control @error('dni') is-invalid @enderror" required maxlength="15">
                            @error('dni')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teléfono *</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teléfono Alt.</label>
                            <input type="text" name="telefono_alt" value="{{ old('telefono_alt') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Usuario del Sistema (opcional)</label>
                            <select name="user_id" class="form-select">
                                <option value="">Sin cuenta de usuario</option>
                                @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">Vehículo y Zona</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo de Vehículo *</label>
                            <select name="tipo_vehiculo" class="form-select" required>
                                @foreach(['moto'=>'🏍️ Moto','bicicleta'=>'🚲 Bicicleta','auto'=>'🚗 Auto','pie'=>'🚶 A pie'] as $v=>$l)
                                <option value="{{ $v }}" {{ old('tipo_vehiculo')===$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Placa</label>
                            <input type="text" name="placa" value="{{ old('placa') }}" class="form-control" placeholder="ABC-123">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Zona Asignada</label>
                            <input type="text" name="zona_asignada" value="{{ old('zona_asignada') }}" class="form-control" placeholder="Miraflores, San Isidro...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Foto del Repartidor</div>
                <div class="card-body">
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg me-1"></i>Registrar Repartidor</button>
                    <a href="{{ route('repartidores.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
