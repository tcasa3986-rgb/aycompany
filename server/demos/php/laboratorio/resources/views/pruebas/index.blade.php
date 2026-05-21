@extends('layouts.app')

@section('title', 'Catálogo de Pruebas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Catálogo de Pruebas</h1>
        <p class="text-secondary">Administración de precios y perfiles analíticos</p>
    </div>
    <div>
        <a href="{{ route('pruebas.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva Prueba</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('pruebas.index') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Buscar por código o nombre..." value="{{ request('search') }}" style="width: 350px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i></button>
        </form>
    </div>
    
    @if(session('success'))
        <div style="background: rgba(46, 213, 115, 0.1); color: var(--success); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Área</th>
                    <th>Nombre del Examen</th>
                    <th>Tipo Muestra</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pruebas as $prueba)
                <tr>
                    <td><strong>{{ $prueba->codigo }}</strong></td>
                    <td>{{ $prueba->area->nombre }}</td>
                    <td>{{ $prueba->nombre }}</td>
                    <td>{{ $prueba->muestra_tipo }}</td>
                    <td><strong class="text-success">S/ {{ number_format($prueba->precio, 2) }}</strong></td>
                    <td>
                        @if($prueba->activo)
                            <span class="status-badge status-completed">Activo</span>
                        @else
                            <span class="status-badge status-critical">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <button class="action-btn text-info" title="Editar"><i class="fa-solid fa-pen-to-square"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No se encontraron pruebas en el catálogo.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pruebas->hasPages())
    <div style="margin-top: 20px;">
        {{ $pruebas->links() }}
    </div>
    @endif
</div>
@endsection
