@extends('layouts.app')
@section('title', 'Convenios')
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Convenios</h1>
        <p class="text-secondary">Aseguradoras, empresas y entidades con descuento especial</p>
    </div>
    <a href="{{ route('convenios.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo Convenio</a>
</div>

@if(session('success'))
    <div style="background:rgba(46,213,115,0.12);border-left:4px solid var(--success);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--success);"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Lista de Convenios ({{ $convenios->total() }})</span>
        <form method="GET" style="display:flex;gap:8px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar convenio..." style="background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;width:220px;">
            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>RUC</th>
                    <th>Tipo</th>
                    <th>Descuento</th>
                    <th>Contacto</th>
                    <th>Órdenes</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($convenios as $conv)
                <tr>
                    <td><strong>{{ $conv->nombre }}</strong></td>
                    <td>{{ $conv->ruc ?? '—' }}</td>
                    <td><span style="background:rgba(142,84,233,0.15);color:var(--accent-primary);padding:3px 10px;border-radius:20px;font-size:0.8rem;">{{ $conv->tipo }}</span></td>
                    <td><strong class="text-success">{{ $conv->descuento_porcentaje }}%</strong></td>
                    <td>{{ $conv->contacto_nombre ?? '—' }} {{ $conv->contacto_telefono ? '(' . $conv->contacto_telefono . ')' : '' }}</td>
                    <td>{{ $conv->ordenes_count }}</td>
                    <td>
                        <span class="status-badge {{ $conv->activo ? 'status-completed' : 'status-critical' }}">
                            {{ $conv->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('convenios.edit', $conv) }}" class="action-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('convenios.destroy', $conv) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn {{ $conv->activo ? 'text-danger' : 'text-success' }}" title="{{ $conv->activo ? 'Desactivar' : 'Activar' }}" onclick="return confirm('¿Cambiar estado del convenio?')">
                                <i class="fa-solid {{ $conv->activo ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">No hay convenios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($convenios->hasPages())<div style="padding:1rem;">{{ $convenios->links() }}</div>@endif
</div>
@endsection
