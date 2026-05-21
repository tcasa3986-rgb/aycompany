@extends('layouts.app')

@section('title', 'Toma de Muestras')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Toma de Muestras</h1>
        <p class="text-secondary">Pacientes en sala de espera y registro de tubos</p>
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
                    <th>Estado Orden</th>
                    <th>Estado Muestras</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                <tr>
                    <td><strong>{{ $orden->numero_orden }}</strong></td>
                    <td>{{ $orden->paciente->nombre_completo ?? 'N/A' }}</td>
                    <td><span style="color: {{ $orden->prioridad == 'Urgente' ? 'var(--danger)' : 'var(--text-secondary)' }}">{{ $orden->prioridad }}</span></td>
                    <td>
                        <span class="status-badge {{ $orden->estado == 'Pendiente' ? 'status-pending' : 'status-completed' }}">
                            {{ $orden->estado }}
                        </span>
                    </td>
                    <td>
                        @if($orden->muestras()->count() > 0)
                            <span class="text-success"><i class="fa-solid fa-vial"></i> Tomadas ({{ $orden->muestras()->count() }})</span>
                        @else
                            <span class="text-warning"><i class="fa-solid fa-clock"></i> Esperando toma</span>
                        @endif
                    </td>
                    <td>
                        @if($orden->muestras()->count() == 0)
                        <form action="{{ route('muestras.tomar', $orden->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">
                                <i class="fa-solid fa-syringe"></i> Tomar Muestra
                            </button>
                        </form>
                        @else
                        <a href="{{ route('muestras.etiquetas', $orden->id) }}" target="_blank" class="btn" style="background: rgba(255,255,255,0.1); padding: 6px 12px; font-size: 0.8rem; color: white;">
                            <i class="fa-solid fa-barcode"></i> Imprimir Etiquetas
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No hay órdenes pendientes de toma de muestra en este momento.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
