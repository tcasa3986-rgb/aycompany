@extends('layouts.app')
@section('title', 'Áreas de Laboratorio')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Áreas de Laboratorio</h1><p class="text-secondary">Secciones analíticas del laboratorio</p></div>
    <a href="{{ route('areas.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva Área</a>
</div>
@if(session('success'))
    <div style="background:rgba(46,213,115,0.12);border-left:4px solid var(--success);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--success);"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--danger);"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
@endif
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
    @forelse($areas as $area)
    <div class="card">
        <div style="padding:1.5rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div style="width:40px;height:40px;border-radius:10px;background:{{ $area->color ?? 'var(--gradient-primary)' }};display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-flask" style="color:#fff;"></i>
                </div>
                <span class="status-badge {{ $area->activo ? 'status-completed' : 'status-critical' }}">{{ $area->activo ? 'Activa' : 'Inactiva' }}</span>
            </div>
            <h3 style="margin:0 0 4px;font-size:1.05rem;">{{ $area->nombre }}</h3>
            <p class="text-muted" style="font-size:0.85rem;margin:0 0 1rem;">{{ $area->descripcion ?? 'Sin descripción' }}</p>
            <div style="display:flex;gap:1rem;font-size:0.85rem;">
                <span><strong>{{ $area->pruebas_count }}</strong> <span class="text-muted">Pruebas</span></span>
                <span><strong>{{ $area->reactivos_count }}</strong> <span class="text-muted">Reactivos</span></span>
            </div>
            <div style="display:flex;gap:8px;margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);">
                <a href="{{ route('areas.edit', $area) }}" class="btn btn-secondary" style="flex:1;text-align:center;padding:7px;font-size:0.85rem;"><i class="fa-solid fa-pen"></i> Editar</a>
                @if($area->pruebas_count == 0 && $area->reactivos_count == 0)
                <form method="POST" action="{{ route('areas.destroy', $area) }}" style="flex:1;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn text-danger" style="width:100%;background:rgba(255,71,87,0.1);border:1px solid rgba(255,71,87,0.3);padding:7px;border-radius:8px;font-size:0.85rem;cursor:pointer;" onclick="return confirm('¿Eliminar área?')">
                        <i class="fa-solid fa-trash"></i> Eliminar
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="grid-column:1/-1;">
        <div style="padding:3rem;text-align:center;color:var(--text-muted);">
            <i class="fa-solid fa-flask-vial" style="font-size:2rem;margin-bottom:1rem;display:block;opacity:0.4;"></i>
            <p>No hay áreas registradas. Crea la primera área del laboratorio.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
