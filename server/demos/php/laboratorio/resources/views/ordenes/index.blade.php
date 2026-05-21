@extends('layouts.app')

@section('title', 'Órdenes Médicas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Órdenes Médicas</h1>
        <p class="text-secondary">Gestión de análisis e ingresos</p>
    </div>
    <div>
        <a href="{{ route('ordenes.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva Orden</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('ordenes.index') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Nro orden, paciente..." value="{{ request('search') }}" style="width: 300px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i></button>
        </form>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nro Orden</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Subtotal_Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                <tr>
                    <td><strong>{{ $orden->numero_orden }}</strong></td>
                    <td>{{ optional($orden->paciente)->nombre_completo }}</td>
                    <td>{{ optional($orden->medico)->nombre_completo ?? 'No especificado' }}</td>
                    <td>{{ $orden->fecha_registro->format('d/m/Y H:i') }}</td>
                    <td>
                        @if(in_array($orden->estado, ['Completado', 'Entregado']))
                            <span class="status-badge status-completed">{{ $orden->estado }}</span>
                        @elseif(in_array($orden->estado, ['Pendiente', 'En proceso']))
                            <span class="status-badge status-pending">{{ $orden->estado }}</span>
                        @else
                            <span class="status-badge status-critical">{{ $orden->estado }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge" style="position:static; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; background: {{ $orden->prioridad == 'Urgente' || $orden->prioridad == 'Emergencia' ? 'var(--danger)' : 'rgba(255,255,255,0.1)' }}; color: white;">{{ $orden->prioridad }}</span>
                    </td>
                    <td>S/ {{ number_format($orden->total, 2) }}</td>
                    <td>
                        <a href="{{ route('ordenes.show', $orden->id) }}" class="action-btn text-primary" title="Ver Detalle"><i class="fa-solid fa-eye"></i></a>
                        @if($orden->estado == 'Pendiente')
                            <a href="{{ route('ordenes.edit', $orden->id) }}" class="action-btn text-warning" title="Editar"><i class="fa-solid fa-edit"></i></a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No se encontraron órdenes.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($ordenes->hasPages())
    <div style="margin-top: 20px;">
        {{ $ordenes->links() }}
    </div>
    @endif
</div>
@endsection
