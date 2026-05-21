@extends('layouts.app')
@section('title', 'Nuevo Alumno')
@section('page-title', 'Registrar Alumno')

@section('content')

<div style="max-width:860px;">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<form method="POST" action="{{ route('alumnos.store') }}">
@csrf

{{-- Datos personales --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-id-card" style="color:#3b82f6;margin-right:8px;"></i>Datos Personales</span>
    </div>
    <div class="card-body">
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">DNI *</label>
                <input type="text" name="dni" class="form-control {{ $errors->has('dni') ? 'is-invalid' : '' }}"
                    value="{{ old('dni') }}" maxlength="20" required placeholder="12345678">
                @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Género *</label>
                <select name="genero" class="form-control {{ $errors->has('genero') ? 'is-invalid' : '' }}" required>
                    <option value="">Seleccionar...</option>
                    <option value="M" {{ old('genero')=='M' ? 'selected':'' }}>Masculino</option>
                    <option value="F" {{ old('genero')=='F' ? 'selected':'' }}>Femenino</option>
                </select>
                @error('genero') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Nombres *</label>
                <input type="text" name="nombres" class="form-control {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                    value="{{ old('nombres') }}" required>
                @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Apellidos *</label>
                <input type="text" name="apellidos" class="form-control {{ $errors->has('apellidos') ? 'is-invalid' : '' }}"
                    value="{{ old('apellidos') }}" required>
                @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Fecha de Nacimiento *</label>
                <input type="date" name="fecha_nacimiento" class="form-control {{ $errors->has('fecha_nacimiento') ? 'is-invalid' : '' }}"
                    value="{{ old('fecha_nacimiento') }}" required>
                @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="987654321">
            </div>
            <div class="form-group">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}">
        </div>
    </div>
</div>

{{-- Datos del apoderado --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-user-friends" style="color:#10b981;margin-right:8px;"></i>Datos del Apoderado</span>
    </div>
    <div class="card-body">
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Nombre del Apoderado</label>
                <input type="text" name="apoderado_nombre" class="form-control" value="{{ old('apoderado_nombre') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Parentesco</label>
                <select name="apoderado_parentesco" class="form-control">
                    <option value="">Seleccionar...</option>
                    <option value="Padre" {{ old('apoderado_parentesco')=='Padre' ? 'selected':'' }}>Padre</option>
                    <option value="Madre" {{ old('apoderado_parentesco')=='Madre' ? 'selected':'' }}>Madre</option>
                    <option value="Abuelo/a" {{ old('apoderado_parentesco')=='Abuelo/a' ? 'selected':'' }}>Abuelo/a</option>
                    <option value="Tío/a" {{ old('apoderado_parentesco')=='Tío/a' ? 'selected':'' }}>Tío/a</option>
                    <option value="Otro" {{ old('apoderado_parentesco')=='Otro' ? 'selected':'' }}>Otro</option>
                </select>
            </div>
        </div>
        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">DNI Apoderado</label>
                <input type="text" name="apoderado_dni" class="form-control" value="{{ old('apoderado_dni') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono Apoderado</label>
                <input type="text" name="apoderado_telefono" class="form-control" value="{{ old('apoderado_telefono') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email Apoderado</label>
                <input type="email" name="apoderado_email" class="form-control" value="{{ old('apoderado_email') }}">
            </div>
        </div>
    </div>
</div>

<div style="display:flex;gap:12px;justify-content:flex-end;">
    <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Guardar Alumno
    </button>
</div>

</form>
</div>
@endsection
