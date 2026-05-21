@extends('layouts.app')
@section('title', 'Editar Usuario')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-pencil me-2 text-warning"></i>Editar Usuario</h4>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>
<form method="POST" action="{{ route('usuarios.update', $usuario) }}">
    @csrf @method('PUT')
    <div class="row g-3 justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">Datos del Usuario</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name', $usuario->name) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $usuario->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rol *</label>
                        <select name="rol" class="form-select" required>
                            @foreach($roles as $rol)
                            <option value="{{ $rol->name }}" {{ $usuario->hasRole($rol->name)?'selected':'' }}>{{ ucfirst($rol->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva Contraseña <span class="text-muted small">(dejar vacío para no cambiar)</span></label>
                        <input type="password" name="password" class="form-control" minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ $usuario->activo ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="activo">Usuario activo</label>
                    </div>
                    <button type="submit" class="btn btn-warning w-100"><i class="bi bi-check-lg me-1"></i>Actualizar Usuario</button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
