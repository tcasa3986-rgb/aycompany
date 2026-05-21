@extends('layouts.app')
@section('title', 'Pagos')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Pagos</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-cash-coin me-2 text-primary"></i>Registro de Pagos</h4>
</div>

<!-- Resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card" style="background:linear-gradient(135deg,#198754,#146c43)">
            <div class="kpi-label">Ingresos Hoy</div>
            <div class="kpi-value">S/ {{ number_format($resumen['total_hoy'], 2) }}</div>
            <i class="bi bi-calendar-check kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="background:linear-gradient(135deg,#0d6efd,#0a58ca)">
            <div class="kpi-label">Ingresos del Mes</div>
            <div class="kpi-value">S/ {{ number_format($resumen['total_mes'], 2) }}</div>
            <i class="bi bi-graph-up kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted small mb-3">Por Método de Pago (mes)</h6>
                @foreach($resumen['por_metodo'] as $m)
                <div class="d-flex justify-content-between small mb-1">
                    <span><i class="bi bi-circle-fill me-1" style="font-size:0.5rem"></i>{{ ucfirst($m->metodo) }}</span>
                    <strong>S/ {{ number_format($m->total, 2) }} <span class="text-muted">({{ $m->cantidad }})</span></strong>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="metodo" class="form-select">
                    <option value="">Todos los métodos</option>
                    @foreach(['efectivo','tarjeta','transferencia','yape','plin'] as $m)
                    <option value="{{ $m }}" {{ request('metodo')===$m?'selected':'' }}>{{ ucfirst($m) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="completado" {{ request('estado')==='completado'?'selected':'' }}>Completado</option>
                    <option value="pendiente" {{ request('estado')==='pendiente'?'selected':'' }}>Pendiente</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control" placeholder="Desde">
            </div>
            <div class="col-md-2">
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control" placeholder="Hasta">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="{{ route('pagos.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i></a>
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
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Comprobante</th>
                        <th>Registrado por</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pagos as $pago)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $pago->id }}</td>
                        <td>
                            @if($pago->pedido)
                            <a href="{{ route('pedidos.show', $pago->pedido) }}" class="text-decoration-none fw-semibold">{{ $pago->pedido->numero }}</a>
                            @else —
                            @endif
                        </td>
                        <td class="small">{{ $pago->pedido?->cliente?->nombre_completo }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="{{ $pago->metodo_icono }} me-1"></i>{{ ucfirst($pago->metodo) }}
                            </span>
                        </td>
                        <td><strong class="text-success">S/ {{ number_format($pago->monto, 2) }}</strong>
                            @if($pago->vuelto > 0)<div class="text-muted small">Vuelto: S/ {{ number_format($pago->vuelto, 2) }}</div>@endif
                        </td>
                        <td class="small">
                            @if($pago->comprobante_tipo && $pago->comprobante_tipo !== 'ninguno')
                            <span class="badge bg-light text-dark border">{{ ucfirst($pago->comprobante_tipo) }}</span>
                            @if($pago->comprobante_numero)<div class="text-muted">{{ $pago->comprobante_numero }}</div>@endif
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="small">{{ $pago->registradoPor?->name ?? '—' }}</td>
                        <td><span class="badge bg-{{ $pago->estado === 'completado' ? 'success' : 'warning' }}">{{ ucfirst($pago->estado) }}</span></td>
                        <td class="small text-muted">{{ $pago->fecha_pago->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-wallet2 display-4 d-block mb-2"></i>No hay pagos registrados
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pagos->hasPages())
    <div class="card-footer d-flex justify-content-between">
        <small class="text-muted">{{ $pagos->total() }} registros</small>
        {{ $pagos->links() }}
    </div>
    @endif
</div>
@endsection
