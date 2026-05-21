@extends('layouts.app')
@section('title', 'Inventario')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Inventario</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-boxes me-2 text-primary"></i>Control de Inventario</h4>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card text-center"><div class="card-body">
        <i class="bi bi-box-seam text-primary fs-2"></i>
        <div class="text-muted small mt-1">Productos</div>
        <h4 class="mb-0">{{ $resumen['total_productos'] }}</h4>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center border-warning"><div class="card-body">
        <i class="bi bi-exclamation-triangle text-warning fs-2"></i>
        <div class="text-muted small mt-1">Stock bajo</div>
        <h4 class="mb-0 text-warning">{{ $resumen['stock_bajo'] }}</h4>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center border-danger"><div class="card-body">
        <i class="bi bi-x-circle text-danger fs-2"></i>
        <div class="text-muted small mt-1">Agotados</div>
        <h4 class="mb-0 text-danger">{{ $resumen['agotados'] }}</h4>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center border-success"><div class="card-body">
        <i class="bi bi-cash-stack text-success fs-2"></i>
        <div class="text-muted small mt-1">Valor inventario</div>
        <h4 class="mb-0 text-success">S/ {{ number_format($resumen['valor_inventario'], 2) }}</h4>
    </div></div></div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre o código" class="form-control">
            </div>
            <div class="col-md-3">
                <select name="filtro" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos los productos</option>
                    <option value="bajo" {{ request('filtro')==='bajo'?'selected':'' }}>Stock bajo o crítico</option>
                    <option value="agotado" {{ request('filtro')==='agotado'?'selected':'' }}>Agotados</option>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filtrar</button></div>
            <div class="col-md-2"><a href="{{ route('inventario.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a></div>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Mínimo</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $p)
                <tr>
                    <td class="ps-3"><code>{{ $p->codigo }}</code></td>
                    <td>{{ $p->nombre }}</td>
                    <td><small class="text-muted">{{ $p->categoria->nombre ?? '—' }}</small></td>
                    <td class="text-center">
                        @php
                            $clase = $p->stock == 0 ? 'danger' : ($p->stock <= $p->stock_minimo ? 'warning' : 'success');
                        @endphp
                        <span class="badge bg-{{ $clase }}">{{ $p->stock }}</span>
                    </td>
                    <td class="text-center text-muted">{{ $p->stock_minimo }}</td>
                    <td class="text-end">S/ {{ number_format($p->precio,2) }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('inventario.kardex', $p) }}" class="btn btn-sm btn-outline-info" title="Kardex"><i class="bi bi-clipboard-data"></i></a>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#m{{$p->id}}" title="Ajustar stock"><i class="bi bi-plus-slash-minus"></i></button>
                    </td>
                </tr>

                <!-- Modal ajuste -->
                <div class="modal fade" id="m{{$p->id}}" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content">
                        <form method="POST" action="{{ route('inventario.ajustar', $p) }}">
                            @csrf
                            <div class="modal-header"><h5 class="modal-title">Ajustar stock — {{ $p->nombre }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <div class="alert alert-info">Stock actual: <strong>{{ $p->stock }}</strong></div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Tipo *</label>
                                        <select name="tipo" class="form-select" required>
                                            <option value="entrada">Entrada (compra/reposición)</option>
                                            <option value="ajuste">Ajuste manual</option>
                                            <option value="merma">Merma / pérdida</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Cantidad *</label>
                                        <input type="number" name="cantidad" class="form-control" required min="1" value="1">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Costo unitario (opcional)</label>
                                        <input type="number" step="0.01" name="costo_unitario" class="form-control">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Motivo *</label>
                                        <input type="text" name="motivo" class="form-control" required placeholder="Ej: Compra factura 0001">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Registrar</button>
                            </div>
                        </form>
                    </div></div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $productos->links() }}</div>
</div>
@endsection
