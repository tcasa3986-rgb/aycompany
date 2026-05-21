@extends('layouts.app')
@section('title', 'Nuevo Concepto')
@section('page-title', 'Nuevo Concepto de Pago')

@section('content')
<div style="max-width:560px;">
<div style="margin-bottom:16px;">
    <a href="{{ route('conceptos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<form method="POST" action="{{ route('conceptos.store') }}">
@csrf
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-tag" style="color:#3b82f6;margin-right:8px;"></i>Datos del Concepto</span>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Nombre del Concepto *</label>
            <input type="text" name="nombre" class="form-control {{ $errors->has('nombre')?'is-invalid':'' }}"
                value="{{ old('nombre') }}" required placeholder="Ej: Mensualidad Primaria">
            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Tipo *</label>
                <select name="tipo" class="form-control" required>
                    <option value="">Seleccionar...</option>
                    <option value="mensualidad" {{ old('tipo')==='mensualidad' ? 'selected':'' }}>Mensualidad</option>
                    <option value="matricula"   {{ old('tipo')==='matricula'   ? 'selected':'' }}>Matrícula</option>
                    <option value="taller"      {{ old('tipo')==='taller'      ? 'selected':'' }}>Taller</option>
                    <option value="otros"       {{ old('tipo')==='otros'       ? 'selected':'' }}>Otros</option>
                </select>
                @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Monto (S/.) *</label>
                <input type="number" name="monto" class="form-control {{ $errors->has('monto')?'is-invalid':'' }}"
                    step="0.01" min="0" value="{{ old('monto') }}" required placeholder="0.00">
                @error('monto') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"
                placeholder="Descripción opcional del concepto...">{{ old('descripcion') }}</textarea>
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <input type="checkbox" name="activo" value="1" {{ old('activo',1) ? 'checked':'' }}
                    style="width:18px;height:18px;accent-color:#3b82f6;">
                <span class="form-label" style="margin-bottom:0;">Concepto activo</span>
            </label>
        </div>
    </div>
</div>
<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
    <a href="{{ route('conceptos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Concepto</button>
</div>
</form>
</div>
@endsection
