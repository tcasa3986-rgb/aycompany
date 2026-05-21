@extends('layouts.app')
@section('title', 'Nuevo Usuario')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">Nuevo</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-person-plus me-2 text-primary"></i>Nuevo Usuario</h4>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>
<form method="POST" action="{{ route('usuarios.store') }}">
    @csrf
    <div class="row g-3 justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">Datos del Usuario</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre completo *</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rol *</label>
                        <select name="rol" class="form-select @error('rol') is-invalid @enderror" required>
                            <option value="">Seleccionar rol...</option>
                            @foreach($roles as $rol)
                            <option value="{{ $rol->name }}" {{ old('rol')===$rol->name?'selected':'' }}>{{ ucfirst($rol->name) }}</option>
                            @endforeach
                        </select>
                        @error('rol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña *</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirmar Contraseña *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg me-1"></i>Crear Usuario</button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
