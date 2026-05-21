@extends('layouts.app')

@section('title', 'Ingreso de Resultados')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Ingreso de Resultados</h1>
        <p class="text-secondary">Órdenes pendientes de análisis y validación</p>
    </div>
</div>

<div class="card">
    @if(session('success'))
        <div style="background: rgba(46, 213, 115, 0.1); color: var(--success); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Paciente</th>
                    <th>Prioridad</th>
                    <th>Avance</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                @php
                    $total = $orden->detalles()->count();
                    $completados = $orden->detalles()->where('estado', 'Completado')->count();
                    $porcentaje = $total > 0 ? round(($completados / $total) * 100) : 0;
                @endphp
                <tr>
                    <td><strong>{{ $orden->numero_orden }}</strong></td>
                    <td>{{ $orden->paciente->nombre_completo ?? 'N/A' }}</td>
                    <td><span style="color: {{ $orden->prioridad == 'Urgente' ? 'var(--danger)' : 'var(--text-secondary)' }}">{{ $orden->prioridad }}</span></td>
                    <td>
                        <div style="width: 100%; max-width: 150px; background: rgba(255,255,255,0.1); border-radius: 10px; height: 8px; margin-bottom: 5px;">
                            <div style="width: {{ $porcentaje }}%; background: var(--accent-gradient); border-radius: 10px; height: 100%;"></div>
                        </div>
                        <small style="color: var(--text-muted);">{{ $completados }} de {{ $total }} pruebas</small>
                    </td>
                    <td>
                        <a href="{{ route('resultados.create', $orden->id) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">
                            <i class="fa-solid fa-microscope"></i> Ingresar Resultados
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No hay órdenes en proceso de análisis.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
