@extends('layouts.app')
@section('title', 'Clientes')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Clientes</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-people me-2 text-primary"></i>Clientes</h4>
    @can('crear clientes')
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Cliente
    </a>
    @endcan
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar por nombre, teléfono, email...">
                </div>
            </div>
            <div class="col-md-2">
                <select name="tipo" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="regular" {{ request('tipo')=='regular'?'selected':'' }}>Regular</option>
                    <option value="frecuente" {{ request('tipo')=='frecuente'?'selected':'' }}>Frecuente</option>
                    <option value="vip" {{ request('tipo')=='vip'?'selected':'' }}>VIP</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado')=='activo'?'selected':'' }}>Activos</option>
                    <option value="inactivo" {{ request('estado')=='inactivo'?'selected':'' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Cliente</th>
                        <th>Contacto</th>
                        <th>Distrito</th>
                        <th>Tipo</th>
                        <th>Pedidos</th>
                        <th>Estado</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $cliente->id }}</td>
                        <td>
                            <a href="{{ route('clientes.show', $cliente) }}" class="fw-semibold text-decoration-none">
                                {{ $cliente->nombre_completo }}
                            </a>
                            @if($cliente->email)<div class="text-muted small">{{ $cliente->email }}</div>@endif
                        </td>
                        <td><i class="bi bi-telephone text-muted me-1"></i>{{ $cliente->telefono }}</td>
                        <td>{{ $cliente->distrito ?? '—' }}</td>
                        <td>
                            @php $tipoBadge = ['regular'=>'secondary','frecuente'=>'info','vip'=>'warning'][$cliente->tipo] ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $tipoBadge }}">{{ ucfirst($cliente->tipo) }}</span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $cliente->pedidos_count }}</span></td>
                        <td>
                            <span class="badge bg-{{ $cliente->activo ? 'success' : 'secondary' }}">
                                {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>
                            @can('editar clientes')
                            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-warning ms-1" title="Editar"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('eliminar clientes')
                            <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="d-inline" onsubmit="return confirm('¿Eliminar cliente {{ $cliente->nombre }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger ms-1" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox display-5 d-block mb-2"></i>No se encontraron clientes
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($clientes->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Mostrando {{ $clientes->firstItem() }}–{{ $clientes->lastItem() }} de {{ $clientes->total() }}</small>
        {{ $clientes->links() }}
    </div>
    @endif
</div>
@endsection
