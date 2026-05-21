@extends('layouts.app')
@section('title', 'Nueva Área')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Nueva Área de Laboratorio</h1></div>
    <a href="{{ route('areas.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>
<div class="card" style="max-width:500px;">
    <div class="card-header"><span class="card-title">Datos del Área</span></div>
    <form method="POST" action="{{ route('areas.store') }}" style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
        @csrf
        @if($errors->any())
            <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;color:var(--danger);">
                <ul style="margin:0;padding-left:1.2rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="form-group">
            <label>Nombre del Área <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required placeholder="Ej: Hematología, Bioquímica...">
        </div>
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3" placeholder="Descripción breve del área...">{{ old('descripcion') }}</textarea>
        </div>
        <div class="form-group">
            <label>Color identificador</label>
            <div style="display:flex;align-items:center;gap:10px;">
                <input type="color" name="color" value="{{ old('color', '#8e54e9') }}" style="width:50px;height:40px;border:none;border-radius:8px;cursor:pointer;background:none;">
                <span class="text-muted" style="font-size:0.85rem;">Se usará para identificar el área visualmente</span>
            </div>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('areas.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar Área</button>
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
