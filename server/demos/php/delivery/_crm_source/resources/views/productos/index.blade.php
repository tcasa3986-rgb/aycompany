@extends('layouts.app')
@section('title', 'Productos')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Productos</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-grid me-2 text-primary"></i>Productos</h4>
    @can('crear productos')
    <a href="{{ route('productos.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo Producto</a>
    @endcan
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Nombre o código...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="categoria_id" class="form-select">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria_id')==$cat->id?'selected':'' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="disponible" class="form-select">
                    <option value="">Disponibilidad</option>
                    <option value="1" {{ request('disponible')==='1'?'selected':'' }}>Disponible</option>
                    <option value="0" {{ request('disponible')==='0'?'selected':'' }}>No disponible</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i></a>
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
                        <th class="ps-3">Código</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Precio Delivery</th>
                        <th>Stock</th>
                        <th>Disponible</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($productos as $p)
                    <tr>
                        <td class="ps-3"><code class="small">{{ $p->codigo }}</code></td>
                        <td>
                            <div class="fw-semibold">{{ $p->nombre }}</div>
                            @if($p->descripcion)<div class="text-muted small">{{ Str::limit($p->descripcion, 50) }}</div>@endif
                        </td>
                        <td>
                            @if($p->categoria)
                            <span class="badge rounded-pill" style="background:{{ $p->categoria->color }}20;color:{{ $p->categoria->color }};border:1px solid {{ $p->categoria->color }}">
                                {{ $p->categoria->nombre }}
                            </span>
                            @endif
                        </td>
                        <td><strong>S/ {{ number_format($p->precio, 2) }}</strong></td>
                        <td>{{ $p->precio_delivery ? 'S/ ' . number_format($p->precio_delivery, 2) : '—' }}</td>
                        <td>
                            <span class="badge bg-{{ $p->stock > 10 ? 'success' : ($p->stock > 0 ? 'warning' : 'danger') }}">
                                {{ $p->stock }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $p->disponible ? 'success' : 'secondary' }}">
                                {{ $p->disponible ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            @can('editar productos')
                            <a href="{{ route('productos.edit', $p) }}" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('eliminar productos')
                            <form method="POST" action="{{ route('productos.destroy', $p) }}" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger ms-1"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox display-5 d-block mb-2"></i>No se encontraron productos
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($productos->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">{{ $productos->total() }} productos</small>
        {{ $productos->links() }}
    </div>
    @endif
</div>
@endsection
