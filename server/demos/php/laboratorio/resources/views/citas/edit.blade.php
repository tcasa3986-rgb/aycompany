@extends('layouts.app')
@section('title', 'Editar Cita')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Editar Cita</h1><p class="text-secondary">{{ $cita->paciente->nombre_completo }}</p></div>
    <a href="{{ route('citas.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>
<div class="card" style="max-width:650px;">
    <div class="card-header"><span class="card-title">Datos de la Cita</span></div>
    <form method="POST" action="{{ route('citas.update', $cita) }}" style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
        @csrf @method('PUT')
        @if($errors->any())
            <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;color:var(--danger);">
                <ul style="margin:0;padding-left:1.2rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group col-span">
                <label>Paciente</label>
                <select name="paciente_id" class="form-control" required>
                    @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" {{ old('paciente_id', $cita->paciente_id) == $p->id ? 'selected' : '' }}>{{ $p->nombre_completo }} — {{ $p->numero_documento }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-span">
                <label>Médico Referidor</label>
                <select name="medico_id" class="form-control">
                    <option value="">— Sin médico —</option>
                    @foreach($medicos as $m)
                        <option value="{{ $m->id }}" {{ old('medico_id', $cita->medico_id) == $m->id ? 'selected' : '' }}>{{ $m->nombre_completo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Fecha y Hora</label>
                <input type="datetime-local" name="fecha_hora" class="form-control" value="{{ old('fecha_hora', $cita->fecha_hora->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div class="form-group">
                <label>Tipo de Atención</label>
                <select name="tipo_atencion" class="form-control" required>
                    @foreach(['Consulta','Toma de Muestras','Entrega de Resultados','Control','Otro'] as $t)
                        <option value="{{ $t }}" {{ old('tipo_atencion', $cita->tipo_atencion) === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-span">
                <label>Motivo</label>
                <textarea name="motivo" class="form-control" rows="2">{{ old('motivo', $cita->motivo) }}</textarea>
            </div>
            <div class="form-group col-span">
                <label>Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $cita->observaciones) }}</textarea>
            </div>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('citas.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Actualizar</button>
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
