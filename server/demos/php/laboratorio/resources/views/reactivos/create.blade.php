@extends('layouts.app')
@section('title', 'Nuevo Reactivo')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Registrar Reactivo</h1></div>
    <a href="{{ route('reactivos.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>
<div class="card" style="max-width:750px;">
    <div class="card-header"><span class="card-title">Datos del Reactivo</span></div>
    <form method="POST" action="{{ route('reactivos.store') }}" style="padding:1.5rem;">
        @csrf
        @if($errors->any())
            <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--danger);">
                <ul style="margin:0;padding-left:1.2rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group">
                <label>Área <span class="text-danger">*</span></label>
                <select name="area_id" class="form-control" required>
                    <option value="">— Seleccionar área —</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Código <span class="text-danger">*</span></label>
                <input type="text" name="codigo" class="form-control" value="{{ old('codigo') }}" required>
            </div>
            <div class="form-group col-span">
                <label>Nombre del Reactivo <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>
            <div class="form-group">
                <label>Marca</label>
                <input type="text" name="marca" class="form-control" value="{{ old('marca') }}">
            </div>
            <div class="form-group">
                <label>Proveedor</label>
                <input type="text" name="proveedor" class="form-control" value="{{ old('proveedor') }}">
            </div>
            <div class="form-group">
                <label>Unidad de Medida <span class="text-danger">*</span></label>
                <input type="text" name="unidad_medida" class="form-control" value="{{ old('unidad_medida', 'Unidad') }}" required>
            </div>
            <div class="form-group">
                <label>N° de Lote</label>
                <input type="text" name="lote" class="form-control" value="{{ old('lote') }}">
            </div>
            <div class="form-group">
                <label>Stock Actual <span class="text-danger">*</span></label>
                <input type="number" name="stock_actual" class="form-control" value="{{ old('stock_actual', 0) }}" min="0" required>
            </div>
            <div class="form-group">
                <label>Stock Mínimo <span class="text-danger">*</span></label>
                <input type="number" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', 5) }}" min="0" required>
            </div>
            <div class="form-group">
                <label>Precio Unitario (S/)</label>
                <input type="number" name="precio_unitario" class="form-control" value="{{ old('precio_unitario', 0) }}" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label>Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}">
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:1rem;justify-content:flex-end;">
            <a href="{{ route('reactivos.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar</button>
        </div>
    </form>
</div>
@endsection
@push('styles')
<style>
.col-span { grid-column: 1 / -1; }
.form-group label { display:block;margin-bottom:6px;font-size:0.9rem;color:var(--text-secondary); }
.form-control { width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;box-sizing:border-box;font-size:0.9rem; }
.form-control:focus { outline:none;border-color:var(--accent-primary); }
</style>
@endpush
