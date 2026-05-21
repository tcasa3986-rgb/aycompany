@extends('layouts.app')
@section('title', 'Editar Concepto')
@section('page-title', 'Editar Concepto de Pago')

@section('content')
<div style="max-width:560px;">
<div style="margin-bottom:16px;">
    <a href="{{ route('conceptos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<form method="POST" action="{{ route('conceptos.update', $concepto) }}">
@csrf @method('PUT')
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-edit" style="color:#3b82f6;margin-right:8px;"></i>Editar Concepto</span>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Nombre del Concepto *</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $concepto->nombre) }}" required>
        </div>
        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Tipo *</label>
                <select name="tipo" class="form-control" required>
                    @foreach(['mensualidad','matricula','taller','otros'] as $t)
                    <option value="{{ $t }}" {{ old('tipo',$concepto->tipo)===$t ? 'selected':'' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Monto (S/.) *</label>
                <input type="number" name="monto" class="form-control" step="0.01" min="0"
                    value="{{ old('monto', $concepto->monto) }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $concepto->descripcion) }}</textarea>
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <input type="checkbox" name="activo" value="1" {{ old('activo',$concepto->activo) ? 'checked':'' }}
                    style="width:18px;height:18px;accent-color:#3b82f6;">
                <span class="form-label" style="margin-bottom:0;">Concepto activo</span>
            </label>
        </div>
    </div>
</div>
<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
    <a href="{{ route('conceptos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
</div>
</form>
</div>
@endsection
