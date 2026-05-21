@extends('layouts.app')
@section('title', 'Nuevo Cliente')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
        <li class="breadcrumb-item active">Nuevo</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-person-plus me-2 text-primary"></i>Nuevo Cliente</h4>
    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<form method="POST" action="{{ route('clientes.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Datos Personales</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" required>
                            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido</label>
                            <input type="text" name="apellido" value="{{ old('apellido') }}" class="form-control @error('apellido') is-invalid @enderror">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono Principal <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control @error('telefono') is-invalid @enderror" required>
                            @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono Alternativo</label>
                            <input type="text" name="telefono_alt" value="{{ old('telefono_alt') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Cliente</label>
                            <select name="tipo" class="form-select">
                                <option value="regular" {{ old('tipo')=='regular'?'selected':'' }}>Regular</option>
                                <option value="frecuente" {{ old('tipo')=='frecuente'?'selected':'' }}>Frecuente</option>
                                <option value="vip" {{ old('tipo')=='vip'?'selected':'' }}>VIP</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Dirección de Entrega</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dirección Completa <span class="text-danger">*</span></label>
                            <textarea name="direccion" rows="2" class="form-control @error('direccion') is-invalid @enderror" required>{{ old('direccion') }}</textarea>
                            @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Referencia</label>
                            <input type="text" name="referencia" value="{{ old('referencia') }}" class="form-control" placeholder="Ej: Frente al parque, casa azul...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Distrito</label>
                            <input type="text" name="distrito" value="{{ old('distrito') }}" class="form-control" placeholder="Ej: Miraflores">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ciudad</label>
                            <input type="text" name="ciudad" value="{{ old('ciudad', 'Lima') }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Notas Internas</div>
                <div class="card-body">
                    <textarea name="notas" rows="5" class="form-control" placeholder="Observaciones del cliente, preferencias, alergias...">{{ old('notas') }}</textarea>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>Guardar Cliente
                    </button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
