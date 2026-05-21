@extends('layouts.app')
@section('title', 'Configuración del Sistema')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Configuración del Sistema</h1><p class="text-secondary">Parámetros generales del laboratorio</p></div>
</div>
@if(session('success'))
    <div style="background:rgba(46,213,115,0.12);border-left:4px solid var(--success);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--success);"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('configuracion.update') }}">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        {{-- Datos del Laboratorio --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-hospital text-gradient"></i> Datos del Laboratorio</span>
            </div>
            <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
                @foreach(['lab_nombre','lab_ruc','lab_direccion','lab_telefono','lab_email','lab_ciudad'] as $clave)
                    @if(isset($configs[$clave]))
                    <div class="form-group">
                        <label>{{ $configs[$clave]->descripcion }}</label>
                        <input type="text" name="configs[{{ $clave }}]" class="form-control" value="{{ old("configs.{$clave}", $configs[$clave]->valor) }}">
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Parámetros Operativos --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-sliders text-gradient"></i> Parámetros Operativos</span>
            </div>
            <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
                @foreach(['igv_porcentaje','moneda_simbolo','dias_entrega'] as $clave)
                    @if(isset($configs[$clave]))
                    <div class="form-group">
                        <label>{{ $configs[$clave]->descripcion }}</label>
                        <input type="{{ $configs[$clave]->tipo === 'numero' ? 'number' : 'text' }}" name="configs[{{ $clave }}]" class="form-control" value="{{ old("configs.{$clave}", $configs[$clave]->valor) }}"
                            {{ $configs[$clave]->tipo === 'numero' ? 'min=0 step=0.01' : '' }}>
                    </div>
                    @endif
                @endforeach

                <div style="border-top:1px solid var(--border);padding-top:1rem;">
                    <div class="form-group">
                        <label>{{ $configs['pie_resultado']->descripcion ?? 'Texto al pie del resultado' }}</label>
                        <textarea name="configs[pie_resultado]" class="form-control" rows="4">{{ old('configs.pie_resultado', $configs['pie_resultado']->valor ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div style="margin-top:1.5rem;display:flex;justify-content:flex-end;">
        <button type="submit" class="btn btn-primary" style="min-width:200px;padding:12px;font-size:1rem;">
            <i class="fa-solid fa-save"></i> Guardar Configuración
        </button>
    </div>
</form>
@endsection
@push('styles')
<style>
.form-group label { display:block;margin-bottom:6px;font-size:0.9rem;color:var(--text-secondary); }
.form-control { width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;box-sizing:border-box;font-size:0.9rem; }
.form-control:focus { outline:none;border-color:var(--accent-primary); }
</style>
@endpush
