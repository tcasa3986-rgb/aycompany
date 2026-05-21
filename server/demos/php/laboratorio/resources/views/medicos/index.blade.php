@extends('layouts.app')
@section('title', 'Médicos Referidores')
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Médicos Referidores</h1>
        <p class="text-secondary">Registro de médicos que derivan pacientes al laboratorio</p>
    </div>
    <a href="{{ route('medicos.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo Médico</a>
</div>

@if(session('success'))
    <div class="alert-success" style="background:rgba(46,213,115,0.12);border-left:4px solid var(--success);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--success);">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert-error" style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--danger);">
        <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Lista de Médicos ({{ $medicos->total() }})</span>
        <form method="GET" style="display:flex;gap:8px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, CMP..." style="background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;width:240px;">
            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>CMP</th>
                    <th>Nombre Completo</th>
                    <th>Especialidad</th>
                    <th>Institución</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicos as $medico)
                <tr>
                    <td><code style="color:var(--accent-primary);">{{ $medico->cmp ?? '—' }}</code></td>
                    <td><strong>{{ $medico->nombre_completo }}</strong></td>
                    <td>{{ $medico->especialidad ?? '—' }}</td>
                    <td>{{ $medico->institucion ?? '—' }}</td>
                    <td>{{ $medico->telefono ?? '—' }}</td>
                    <td>
                        <span class="status-badge {{ $medico->activo ? 'status-completed' : 'status-critical' }}">
                            {{ $medico->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('medicos.edit', $medico) }}" class="action-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('medicos.destroy', $medico) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn {{ $medico->activo ? 'text-danger' : 'text-success' }}" title="{{ $medico->activo ? 'Desactivar' : 'Activar' }}" onclick="return confirm('¿Confirmar cambio de estado?')">
                                <i class="fa-solid {{ $medico->activo ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No hay médicos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($medicos->hasPages())
        <div style="padding:1rem;">{{ $medicos->links() }}</div>
    @endif
</div>
@endsection
