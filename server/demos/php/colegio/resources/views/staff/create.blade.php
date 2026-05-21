@extends('layouts.app')
@section('title', 'Nuevo Personal')
@section('page-title', 'Registrar Personal')

@section('content')
<div style="max-width:860px;">
<div style="margin-bottom:16px;">
    <a href="{{ route('personal.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<form method="POST" action="{{ route('personal.store') }}">
@csrf

<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-id-badge" style="color:#3b82f6;margin-right:8px;"></i>Datos Personales</span>
    </div>
    <div class="card-body">
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">DNI *</label>
                <input type="text" name="dni" class="form-control {{ $errors->has('dni')?'is-invalid':'' }}"
                    value="{{ old('dni') }}" maxlength="20" required>
                @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Tipo *</label>
                <select name="tipo" class="form-control" required>
                    <option value="">Seleccionar...</option>
                    @foreach(['docente'=>'Docente','administrativo'=>'Administrativo','directivo'=>'Directivo','auxiliar'=>'Auxiliar'] as $val => $label)
                    <option value="{{ $val }}" {{ old('tipo')===$val ? 'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Nombres *</label>
                <input type="text" name="nombres" class="form-control {{ $errors->has('nombres')?'is-invalid':'' }}"
                    value="{{ old('nombres') }}" required>
                @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Apellidos *</label>
                <input type="text" name="apellidos" class="form-control {{ $errors->has('apellidos')?'is-invalid':'' }}"
                    value="{{ old('apellidos') }}" required>
                @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Especialidad</label>
                <input type="text" name="especialidad" class="form-control" value="{{ old('especialidad') }}"
                    placeholder="Ej: Matemáticas, Historia...">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}">
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-briefcase" style="color:#10b981;margin-right:8px;"></i>Datos Laborales</span>
    </div>
    <div class="card-body">
        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Fecha de Ingreso *</label>
                <input type="date" name="fecha_ingreso" class="form-control"
                    value="{{ old('fecha_ingreso', date('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Salario (S/.) *</label>
                <input type="number" name="salario" class="form-control" step="0.01" min="0"
                    value="{{ old('salario') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="activo" selected>Activo</option>
                    <option value="inactivo">Inactivo</option>
                    <option value="licencia">Licencia</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div style="display:flex;gap:12px;justify-content:flex-end;">
    <a href="{{ route('personal.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Personal</button>
</div>
</form>
</div>
@endsection
