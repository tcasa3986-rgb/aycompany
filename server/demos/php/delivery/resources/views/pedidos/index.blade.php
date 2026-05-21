@extends('layouts.app')
@section('title', 'Pedidos')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Pedidos</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-bag-check me-2 text-primary"></i>Pedidos</h4>
    @can('crear pedidos')
    <a href="{{ route('pedidos.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo Pedido</a>
    @endcan
</div>

<!-- Filtros rápidos de estado -->
<div class="d-flex gap-2 flex-wrap mb-3">
    <a href="{{ route('pedidos.index') }}" class="btn btn-sm {{ !request('estado') ? 'btn-dark' : 'btn-outline-dark' }}">Todos</a>
    @foreach(['pendiente'=>['warning','Pendientes'],'confirmado'=>['info','Confirmados'],'preparando'=>['primary','Preparando'],'en_camino'=>['info','En Camino'],'entregado'=>['success','Entregados'],'cancelado'=>['danger','Cancelados']] as $est=>$conf)
    <a href="{{ route('pedidos.index', ['estado'=>$est]) }}" class="btn btn-sm {{ request('estado')===$est ? 'btn-'.$conf[0] : 'btn-outline-'.$conf[0] }}">
        {{ $conf[1] }}
    </a>
    @endforeach
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="estado" value="{{ request('estado') }}">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="N° pedido, cliente, teléfono...">
                </div>
            </div>
            <div class="col-md-2">
                <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <select name="repartidor_id" class="form-select">
                    <option value="">Todos los repartidores</option>
                    @foreach($repartidores as $rep)
                    <option value="{{ $rep->id }}" {{ request('repartidor_id')==$rep->id?'selected':'' }}>{{ $rep->nombre_completo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i></a>
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
                        <th class="ps-3">N° Pedido</th>
                        <th>Cliente</th>
                        <th>Dirección</th>
                        <th>Repartidor</th>
                        <th>Total</th>
                        <th>Pago</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pedidos as $pedido)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('pedidos.show', $pedido) }}" class="fw-bold text-decoration-none">{{ $pedido->numero }}</a>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $pedido->cliente?->nombre_completo ?? '—' }}</div>
                            <div class="text-muted small">{{ $pedido->cliente?->telefono }}</div>
                        </td>
                        <td>
                            <div class="small">{{ Str::limit($pedido->direccion_entrega, 35) }}</div>
                            @if($pedido->distrito_entrega)<div class="text-muted small">{{ $pedido->distrito_entrega }}</div>@endif
                        </td>
                        <td>
                            @if($pedido->repartidor)
                                <span class="badge bg-secondary">{{ $pedido->repartidor->nombre }}</span>
                            @else
                                <span class="text-muted small">Sin asignar</span>
                            @endif
                        </td>
                        <td><strong>S/ {{ number_format($pedido->total, 2) }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $pedido->estado_pago === 'pagado' ? 'success' : ($pedido->estado_pago === 'parcial' ? 'warning' : 'danger') }}">
                                {{ ucfirst($pedido->estado_pago) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $pedido->estado_badge }}">{{ $pedido->estado_texto }}</span>
                        </td>
                        <td class="small text-muted">{{ $pedido->created_at->format('d/m H:i') }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-bag-x display-5 d-block mb-2"></i>No se encontraron pedidos
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pedidos->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">{{ $pedidos->total() }} pedidos</small>
        {{ $pedidos->links() }}
    </div>
    @endif
</div>
@endsection
