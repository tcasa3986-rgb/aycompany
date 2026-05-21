@extends('layouts.app')
@section('title', 'Producto: ' . $producto->nombre)

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
        <li class="breadcrumb-item active">{{ $producto->nombre }}</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-box-seam me-2 text-primary"></i>{{ $producto->nombre }}</h4>
    <div class="d-flex gap-2">
        @can('editar productos')
        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Editar</a>
        @endcan
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body">
                @if($producto->imagen)
                    <img src="{{ asset('storage/'.$producto->imagen) }}" class="img-fluid rounded mb-2" style="max-height:260px">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:260px">
                        <i class="bi bi-image text-muted" style="font-size:5rem"></i>
                    </div>
                @endif
                <h5 class="mt-3 mb-1">{{ $producto->nombre }}</h5>
                <span class="badge bg-{{ $producto->disponible ? 'success' : 'secondary' }}">
                    {{ $producto->disponible ? 'Disponible' : 'No disponible' }}
                </span>
                @if(!$producto->activo)<span class="badge bg-danger">Inactivo</span>@endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Información del Producto</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th style="width:35%">Código</th><td><code>{{ $producto->codigo }}</code></td></tr>
                    <tr><th>Categoría</th><td>{{ $producto->categoria->nombre ?? '—' }}</td></tr>
                    <tr><th>Precio base</th><td class="text-success fw-bold">S/ {{ number_format($producto->precio,2) }}</td></tr>
                    <tr><th>Precio delivery</th><td>S/ {{ number_format($producto->precio_delivery ?? $producto->precio, 2) }}</td></tr>
                    <tr><th>Unidad</th><td>{{ ucfirst($producto->unidad) }}</td></tr>
                    <tr><th>Stock actual</th>
                        <td>
                            <span class="badge bg-{{ $producto->stock > 10 ? 'success' : ($producto->stock > 0 ? 'warning' : 'danger') }}">
                                {{ $producto->stock }} {{ $producto->unidad }}
                            </span>
                        </td>
                    </tr>
                    <tr><th>Total vendido</th><td>{{ $totalVendido }} unidades</td></tr>
                    <tr><th>Descripción</th><td>{{ $producto->descripcion ?? '—' }}</td></tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Últimas ventas</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Pedido</th><th>Cliente</th><th>Fecha</th><th class="text-center">Cant.</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @forelse($ventasRecientes as $v)
                        <tr>
                            <td><a href="{{ route('pedidos.show', $v->pedido) }}">{{ $v->pedido->numero }}</a></td>
                            <td>{{ $v->pedido->cliente->nombre ?? '—' }}</td>
                            <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">{{ $v->cantidad }}</td>
                            <td class="text-end">S/ {{ number_format($v->subtotal,2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Sin ventas registradas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
