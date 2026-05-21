@extends('layouts.app')
@section('title', 'Gestión de Usuarios')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Gestión de Usuarios</h1><p class="text-secondary">Administración de cuentas y roles del sistema</p></div>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nuevo Usuario</a>
</div>
@if(session('success'))
    <div style="background:rgba(46,213,115,0.12);border-left:4px solid var(--success);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--success);"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--danger);"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
@endif
<div class="card">
    <div class="card-header">
        <span class="card-title">Usuarios del Sistema ({{ $usuarios->total() }})</span>
        <form method="GET" style="display:flex;gap:8px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email..." style="background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;width:240px;">
            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>Usuario</th><th>Email</th><th>Rol</th><th>Registrado</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.9rem;">
                                {{ substr($usuario->name, 0, 1) }}
                            </div>
                            <div>
                                <strong>{{ $usuario->name }}</strong>
                                @if($usuario->id === auth()->id())<span style="font-size:0.75rem;color:var(--accent-primary);margin-left:6px;">(Tú)</span>@endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $usuario->email }}</td>
                    <td>
                        @foreach($usuario->roles as $role)
                            <span style="background:rgba(142,84,233,0.15);color:var(--accent-primary);padding:3px 10px;border-radius:20px;font-size:0.8rem;">{{ $role->name }}</span>
                        @endforeach
                        @if($usuario->roles->isEmpty())<span class="text-muted" style="font-size:0.85rem;">Sin rol</span>@endif
                    </td>
                    <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                    <td>
                        @php $activo = !is_null($usuario->email_verified_at); @endphp
                        <span class="status-badge {{ $activo ? 'status-completed' : 'status-critical' }}">
                            {{ $activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('usuarios.edit', $usuario) }}" class="action-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                        @if($usuario->id !== auth()->id())
                        <form method="POST" action="{{ route('usuarios.toggle', $usuario) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="action-btn {{ $activo ? 'text-danger' : 'text-success' }}" title="{{ $activo ? 'Desactivar' : 'Activar' }}" onclick="return confirm('¿Cambiar estado del usuario?')">
                                <i class="fa-solid {{ $activo ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No hay usuarios.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($usuarios->hasPages())<div style="padding:1rem;">{{ $usuarios->links() }}</div>@endif
</div>
@endsection
