@extends('layouts.app')
@section('title', 'Agenda de Citas')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Agenda de Citas</h1><p class="text-secondary">Programación y seguimiento de citas de pacientes</p></div>
    <a href="{{ route('citas.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva Cita</a>
</div>

@if(session('success'))
    <div style="background:rgba(46,213,115,0.12);border-left:4px solid var(--success);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--success);"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--danger);"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Citas ({{ $citas->total() }})</span>
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar paciente..." style="background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;width:180px;">
            <input type="date" name="fecha" value="{{ request('fecha') }}" style="background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;">
            <select name="estado" style="background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;">
                <option value="">Todos los estados</option>
                @foreach(['Programada','Confirmada','Atendida','Cancelada','No asistió'] as $e)
                    <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ $e }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-filter"></i></button>
            @if(request()->hasAny(['search','fecha','estado']))<a href="{{ route('citas.index') }}" class="btn btn-secondary">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>Fecha / Hora</th><th>Paciente</th><th>Médico Ref.</th><th>Tipo</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($citas as $cita)
                <tr>
                    <td>
                        <strong>{{ $cita->fecha_hora->format('d/m/Y') }}</strong><br>
                        <small class="text-muted">{{ $cita->fecha_hora->format('H:i') }}</small>
                    </td>
                    <td><strong>{{ $cita->paciente->nombre_completo }}</strong><br><small class="text-muted">{{ $cita->paciente->numero_documento }}</small></td>
                    <td>{{ $cita->medico?->nombre_completo ?? '—' }}</td>
                    <td>{{ $cita->tipo_atencion }}</td>
                    <td>
                        @php $badge = match($cita->estado) { 'Atendida' => 'status-completed', 'Confirmada' => 'status-pending', 'Programada' => 'status-pending', default => 'status-critical' }; @endphp
                        <span class="status-badge {{ $badge }}">{{ $cita->estado }}</span>
                    </td>
                    <td>
                        <a href="{{ route('citas.edit', $cita) }}" class="action-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                        @if(!in_array($cita->estado, ['Atendida','Cancelada']))
                        <div style="display:inline;" class="dropdown-wrapper">
                            <button class="action-btn text-success" onclick="cambiarEstado({{ $cita->id }})" title="Cambiar Estado"><i class="fa-solid fa-circle-check"></i></button>
                        </div>
                        <form method="POST" action="{{ route('citas.destroy', $cita) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn text-danger" title="Cancelar" onclick="return confirm('¿Cancelar esta cita?')"><i class="fa-solid fa-ban"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No hay citas programadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($citas->hasPages())<div style="padding:1rem;">{{ $citas->links() }}</div>@endif
</div>

{{-- Modal cambio de estado --}}
<div id="estadoModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:1000;align-items:center;justify-content:center;">
    <div class="card" style="width:360px;">
        <div class="card-header"><span class="card-title">Cambiar Estado de Cita</span>
            <button onclick="document.getElementById('estadoModal').style.display='none'" style="background:none;border:none;color:var(--text);font-size:1.3rem;cursor:pointer;">×</button>
        </div>
        <form id="estadoForm" method="POST" style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
            @csrf
            <div class="form-group">
                <label>Nuevo Estado</label>
                <select name="estado" class="form-control">
                    @foreach(['Programada','Confirmada','Atendida','No asistió'] as $e)
                        <option>{{ $e }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('estadoModal').style.display='none'" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function cambiarEstado(id) {
    document.getElementById('estadoForm').action = `/citas/${id}/estado`;
    document.getElementById('estadoModal').style.display = 'flex';
}
</script>
@endpush
@push('styles')
<style>
.form-group label { display:block;margin-bottom:6px;font-size:0.9rem;color:var(--text-secondary); }
.form-control { width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;box-sizing:border-box;font-size:0.9rem; }
</style>
@endpush
