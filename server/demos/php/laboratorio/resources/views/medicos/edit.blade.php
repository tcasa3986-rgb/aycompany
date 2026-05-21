@extends('layouts.app')
@section('title', 'Editar Médico')
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Editar Médico Referidor</h1>
        <p class="text-secondary">{{ $medico->nombre_completo }}</p>
    </div>
    <a href="{{ route('medicos.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-header"><span class="card-title">Datos del Médico</span></div>
    <form method="POST" action="{{ route('medicos.update', $medico) }}" style="padding:1.5rem;">
        @csrf @method('PUT')
        @if($errors->any())
            <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--danger);">
                <ul style="margin:0;padding-left:1.2rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group col-2-span">
                <label>CMP</label>
                <input type="text" name="cmp" class="form-control" value="{{ old('cmp', $medico->cmp) }}">
            </div>
            <div class="form-group">
                <label>Nombres <span class="text-danger">*</span></label>
                <input type="text" name="nombres" class="form-control" value="{{ old('nombres', $medico->nombres) }}" required>
            </div>
            <div class="form-group">
                <label>Apellidos <span class="text-danger">*</span></label>
                <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos', $medico->apellidos) }}" required>
            </div>
            <div class="form-group">
                <label>Especialidad</label>
                <input type="text" name="especialidad" class="form-control" value="{{ old('especialidad', $medico->especialidad) }}">
            </div>
            <div class="form-group">
                <label>Institución</label>
                <input type="text" name="institucion" class="form-control" value="{{ old('institucion', $medico->institucion) }}">
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $medico->telefono) }}">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $medico->email) }}">
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:1rem;justify-content:flex-end;">
            <a href="{{ route('medicos.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Actualizar</button>
        </div>
    </form>
</div>
@endsection
@push('styles')
<style>
.col-2-span { grid-column: 1 / -1; }
.form-group label { display:block;margin-bottom:6px;font-size:0.9rem;color:var(--text-secondary); }
.form-control { width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;box-sizing:border-box;font-size:0.9rem; }
.form-control:focus { outline:none;border-color:var(--accent-primary); }
</style>
@endpush
