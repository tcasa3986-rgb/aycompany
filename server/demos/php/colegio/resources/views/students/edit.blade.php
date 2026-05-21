@extends('layouts.app')
@section('title', 'Editar Alumno')
@section('page-title', 'Editar Alumno')

@section('content')
<div style="max-width:860px;">
<div style="display:flex;gap:12px;margin-bottom:20px;">
    <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<form method="POST" action="{{ route('alumnos.update', $alumno) }}">
@csrf @method('PUT')

<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-id-card" style="color:#3b82f6;margin-right:8px;"></i>Datos Personales</span>
        <span style="font-size:12px;color:var(--muted);">Código: {{ $alumno->codigo }}</span>
    </div>
    <div class="card-body">
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">DNI *</label>
                <input type="text" name="dni" class="form-control {{ $errors->has('dni') ? 'is-invalid':'' }}"
                    value="{{ old('dni', $alumno->dni) }}" maxlength="20" required>
                @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Género *</label>
                <select name="genero" class="form-control" required>
                    <option value="M" {{ old('genero',$alumno->genero)==='M' ? 'selected':'' }}>Masculino</option>
                    <option value="F" {{ old('genero',$alumno->genero)==='F' ? 'selected':'' }}>Femenino</option>
                </select>
            </div>
        </div>
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Nombres *</label>
                <input type="text" name="nombres" class="form-control {{ $errors->has('nombres') ? 'is-invalid':'' }}"
                    value="{{ old('nombres', $alumno->nombres) }}" required>
                @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Apellidos *</label>
                <input type="text" name="apellidos" class="form-control {{ $errors->has('apellidos') ? 'is-invalid':'' }}"
                    value="{{ old('apellidos', $alumno->apellidos) }}" required>
                @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Fecha de Nacimiento *</label>
                <input type="date" name="fecha_nacimiento" class="form-control"
                    value="{{ old('fecha_nacimiento', $alumno->fecha_nacimiento?->format('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $alumno->telefono) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    @foreach(['activo','inactivo','trasladado','egresado'] as $e)
                    <option value="{{ $e }}" {{ old('estado',$alumno->estado)===$e ? 'selected':'' }}>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $alumno->email) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $alumno->direccion) }}">
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-user-friends" style="color:#10b981;margin-right:8px;"></i>Datos del Apoderado</span>
    </div>
    <div class="card-body">
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Nombre del Apoderado</label>
                <input type="text" name="apoderado_nombre" class="form-control" value="{{ old('apoderado_nombre', $alumno->apoderado_nombre) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Parentesco</label>
                <select name="apoderado_parentesco" class="form-control">
                    <option value="">Seleccionar...</option>
                    @foreach(['Padre','Madre','Abuelo/a','Tío/a','Otro'] as $p)
                    <option value="{{ $p }}" {{ old('apoderado_parentesco',$alumno->apoderado_parentesco)===$p ? 'selected':'' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">DNI Apoderado</label>
                <input type="text" name="apoderado_dni" class="form-control" value="{{ old('apoderado_dni', $alumno->apoderado_dni) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono Apoderado</label>
                <input type="text" name="apoderado_telefono" class="form-control" value="{{ old('apoderado_telefono', $alumno->apoderado_telefono) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email Apoderado</label>
                <input type="email" name="apoderado_email" class="form-control" value="{{ old('apoderado_email', $alumno->apoderado_email) }}">
            </div>
        </div>
    </div>
</div>

<div style="display:flex;gap:12px;justify-content:flex-end;">
    <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
</div>
</form>
</div>
@endsection
