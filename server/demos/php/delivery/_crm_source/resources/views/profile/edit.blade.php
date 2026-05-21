@extends('layouts.app')
@section('title', 'Mi Perfil')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Mi Perfil</li>
    </ol></nav>
@endsection

@section('content')
<div class="row g-3">
    <!-- Datos personales -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Datos Personales</div>
            <div class="card-body">
                @if(session('status') === 'profile-updated')
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-1"></i> Perfil actualizado correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" value="{{ $user->email }}" class="form-control" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}"
                               class="form-control @error('telefono') is-invalid @enderror">
                        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cambiar contraseña -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-shield-lock me-2 text-warning"></i>Cambiar Contraseña</div>
            <div class="card-body">
                @if(session('status') === 'password-updated')
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-1"></i> Contraseña actualizada correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf @method('DELETE')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña Actual</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva Contraseña</label>
                        <input type="password" name="new_password"
                               class="form-control @error('new_password') is-invalid @enderror" required>
                        @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirmar Contraseña</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-key me-1"></i>Cambiar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
