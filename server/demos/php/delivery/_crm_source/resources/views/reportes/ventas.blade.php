@extends('layouts.app')
@section('title', 'Reporte de Ventas')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li class="breadcrumb-item active">Ventas</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Reporte de Ventas</h4>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Desde</label>
                <input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    @foreach(['pendiente','confirmado','preparando','en_camino','entregado','cancelado'] as $e)
                    <option value="{{ $e }}" {{ request('estado')===$e?'selected':'' }}>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto d-flex align-items-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Generar</button>
            </div>
        </form>
    </div>
</div>

<!-- KPIs del período -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card text-center"><div class="card-body">
            <div class="fw-bold fs-3 text-primary">{{ $resumen['total_pedidos'] }}</div>
            <small class="text-muted">Total Pedidos</small>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card text-center"><div class="card-body">
            <div class="fw-bold fs-3 text-success">{{ $resumen['pedidos_entregados'] }}</div>
            <small class="text-muted">Entregados</small>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card text-center"><div class="card-body">
            <div class="fw-bold fs-3 text-danger">{{ $resumen['pedidos_cancelados'] }}</div>
            <small class="text-muted">Cancelados</small>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card text-center"><div class="card-body">
            <div class="fw-bold fs-3 text-success">S/ {{ number_format($resumen['total_ingresos'], 0) }}</div>
            <small class="text-muted">Ingresos</small>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card text-center"><div class="card-body">
            <div class="fw-bold fs-3 text-info">S/ {{ number_format($resumen['ticket_promedio'], 2) }}</div>
            <small class="text-muted">Ticket Prom.</small>
        </div></div>
    </div>
    <div class="col-md-2">
        <div class="card text-center"><div class="card-body">
            <div class="fw-bold fs-3">S/ {{ number_format($resumen['total_delivery'], 0) }}</div>
            <small class="text-muted">Ingresos Delivery</small>
        </div></div>
    </div>
</div>

<!-- Tabla detallada -->
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Detalle de Pedidos — {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</span>
        <span class="text-muted small">{{ $pedidos->count() }} registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height:500px;overflow-y:auto">
            <table class="table table-hover table-sm mb-0">
                <thead class="sticky-top bg-white">
                    <tr>
                        <th class="ps-3">N° Pedido</th>
                        <th>Cliente</th>
                        <th>Repartidor</th>
                        <th>Subtotal</th>
                        <th>Delivery</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Pago</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pedidos as $p)
                <tr>
                    <td class="ps-3"><a href="{{ route('pedidos.show', $p) }}" class="text-decoration-none">{{ $p->numero }}</a></td>
                    <td class="small">{{ $p->cliente?->nombre_completo }}</td>
                    <td class="small">{{ $p->repartidor?->nombre ?? '—' }}</td>
                    <td class="small">S/ {{ number_format($p->subtotal, 2) }}</td>
                    <td class="small">S/ {{ number_format($p->costo_delivery, 2) }}</td>
                    <td><strong>S/ {{ number_format($p->total, 2) }}</strong></td>
                    <td><span class="badge bg-{{ $p->estado_badge }}">{{ $p->estado_texto }}</span></td>
                    <td><span class="badge bg-{{ $p->estado_pago === 'pagado' ? 'success' : 'warning' }}">{{ ucfirst($p->estado_pago) }}</span></td>
                    <td class="small text-muted">{{ $p->created_at->format('d/m H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">Sin registros en este período</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
