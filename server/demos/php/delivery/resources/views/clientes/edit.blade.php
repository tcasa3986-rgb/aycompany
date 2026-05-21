@extends('layouts.app')
@section('title', 'Editar Cliente')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
        <li class="breadcrumb-item"><a href="{{ route('clientes.show', $cliente) }}">{{ $cliente->nombre_completo }}</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-pencil me-2 text-warning"></i>Editar Cliente</h4>
    <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<form method="POST" action="{{ route('clientes.update', $cliente) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Datos Personales</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre *</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
                            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido</label>
                            <input type="text" name="apellido" value="{{ old('apellido', $cliente->apellido) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono *</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono Alt.</label>
                            <input type="text" name="telefono_alt" value="{{ old('telefono_alt', $cliente->telefono_alt) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" value="{{ old('email', $cliente->email) }}" class="form-control @error('email') is-invalid @enderror">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tipo</label>
                            <select name="tipo" class="form-select">
                                @foreach(['regular','frecuente','vip'] as $t)
                                <option value="{{ $t }}" {{ old('tipo', $cliente->tipo) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="activo" class="form-select">
                                <option value="1" {{ $cliente->activo ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ !$cliente->activo ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">Dirección</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dirección *</label>
                            <textarea name="direccion" rows="2" class="form-control" required>{{ old('direccion', $cliente->direccion) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Referencia</label>
                            <input type="text" name="referencia" value="{{ old('referencia', $cliente->referencia) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Distrito</label>
                            <input type="text" name="distrito" value="{{ old('distrito', $cliente->distrito) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ciudad</label>
                            <input type="text" name="ciudad" value="{{ old('ciudad', $cliente->ciudad) }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Notas</div>
                <div class="card-body">
                    <textarea name="notas" rows="5" class="form-control">{{ old('notas', $cliente->notas) }}</textarea>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-warning w-100"><i class="bi bi-check-lg me-1"></i>Actualizar</button>
                    <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
