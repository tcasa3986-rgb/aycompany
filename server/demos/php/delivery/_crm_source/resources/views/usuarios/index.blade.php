@extends('layouts.app')
@section('title', 'Usuarios')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-person-badge me-2 text-primary"></i>Gestión de Usuarios</h4>
    @can('crear usuarios')
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo Usuario</a>
    @endcan
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Nombre o email...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="rol" class="form-select">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $rol)
                    <option value="{{ $rol->name }}" {{ request('rol')===$rol->name?'selected':'' }}>{{ ucfirst($rol->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i></a>
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
                        <th class="ps-3">Usuario</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($usuarios as $usuario)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $usuario->avatar_url }}" class="avatar-sm" alt="">
                                <span class="fw-semibold">{{ $usuario->name }}</span>
                            </div>
                        </td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->telefono ?? '—' }}</td>
                        <td>
                            @php $rolColor = ['super-admin'=>'danger','admin'=>'primary','operador'=>'info','repartidor'=>'success'][$usuario->getRoleNames()->first()] ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $rolColor }}">{{ $usuario->nombre_rol }}</span>
                        </td>
                        <td><span class="badge bg-{{ $usuario->activo ? 'success' : 'secondary' }}">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</span></td>
                        <td class="small text-muted">{{ $usuario->created_at->format('d/m/Y') }}</td>
                        <td class="text-end pe-3">
                            @can('editar usuarios')
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('eliminar usuarios')
                            @if($usuario->id !== auth()->id())
                            <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" class="d-inline" onsubmit="return confirm('¿Desactivar usuario?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger ms-1"><i class="bi bi-person-dash"></i></button>
                            </form>
                            @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No se encontraron usuarios</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($usuarios->hasPages())
    <div class="card-footer">{{ $usuarios->links() }}</div>
    @endif
</div>
@endsection
