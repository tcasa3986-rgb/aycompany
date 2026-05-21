@extends('layouts.app')
@section('title', 'Editar Área')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Editar Área</h1><p class="text-secondary">{{ $area->nombre }}</p></div>
    <a href="{{ route('areas.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>
<div class="card" style="max-width:500px;">
    <div class="card-header"><span class="card-title">Datos del Área</span></div>
    <form method="POST" action="{{ route('areas.update', $area) }}" style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
        @csrf @method('PUT')
        @if($errors->any())
            <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;color:var(--danger);">
                <ul style="margin:0;padding-left:1.2rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="form-group">
            <label>Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $area->nombre) }}" required>
        </div>
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $area->descripcion) }}</textarea>
        </div>
        <div class="form-group">
            <label>Color</label>
            <input type="color" name="color" value="{{ old('color', $area->color ?? '#8e54e9') }}" style="width:50px;height:40px;border:none;border-radius:8px;cursor:pointer;background:none;">
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('areas.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Actualizar</button>
        </div>
    </form>
</div>
@endsection
@push('styles')
<style>
.form-group label { display:block;margin-bottom:6px;font-size:0.9rem;color:var(--text-secondary); }
.form-control { width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;box-sizing:border-box;font-size:0.9rem; }
.form-control:focus { outline:none;border-color:var(--accent-primary); }
</style>
@endpush
