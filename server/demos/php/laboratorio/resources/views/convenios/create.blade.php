@extends('layouts.app')
@section('title', 'Nuevo Convenio')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Registrar Convenio</h1></div>
    <a href="{{ route('convenios.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>
<div class="card" style="max-width:700px;">
    <div class="card-header"><span class="card-title">Datos del Convenio</span></div>
    <form method="POST" action="{{ route('convenios.store') }}" style="padding:1.5rem;">
        @csrf
        @if($errors->any())
            <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--danger);">
                <ul style="margin:0;padding-left:1.2rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group col-span">
                <label>Nombre del Convenio <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>
            <div class="form-group">
                <label>RUC</label>
                <input type="text" name="ruc" class="form-control" value="{{ old('ruc') }}">
            </div>
            <div class="form-group">
                <label>Tipo <span class="text-danger">*</span></label>
                <select name="tipo" class="form-control" required>
                    @foreach(['Aseguradora','Empresa','Clínica','Municipalidad','Otro'] as $t)
                        <option value="{{ $t }}" {{ old('tipo') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Descuento (%) <span class="text-danger">*</span></label>
                <input type="number" name="descuento_porcentaje" class="form-control" value="{{ old('descuento_porcentaje', 0) }}" min="0" max="100" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Nombre de Contacto</label>
                <input type="text" name="contacto_nombre" class="form-control" value="{{ old('contacto_nombre') }}">
            </div>
            <div class="form-group">
                <label>Teléfono de Contacto</label>
                <input type="text" name="contacto_telefono" class="form-control" value="{{ old('contacto_telefono') }}">
            </div>
            <div class="form-group col-span">
                <label>Condiciones del Convenio</label>
                <textarea name="condiciones" class="form-control" rows="3">{{ old('condiciones') }}</textarea>
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:1rem;justify-content:flex-end;">
            <a href="{{ route('convenios.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar Convenio</button>
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
