@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <h5 class="page-title mb-0"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h5>
@endsection

@section('content')

<!-- KPI CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kpi-card" style="background: linear-gradient(135deg,#0d6efd,#0a58ca)">
            <div class="kpi-label">Pedidos Hoy</div>
            <div class="kpi-value">{{ $kpis['pedidos_hoy'] }}</div>
            <i class="bi bi-bag-check kpi-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card" style="background: linear-gradient(135deg,#ffc107,#e0a800)">
            <div class="kpi-label">Pendientes</div>
            <div class="kpi-value">{{ $kpis['pedidos_pendientes'] }}</div>
            <i class="bi bi-clock-history kpi-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card" style="background: linear-gradient(135deg,#198754,#146c43)">
            <div class="kpi-label">Entregados Hoy</div>
            <div class="kpi-value">{{ $kpis['entregados_hoy'] }}</div>
            <i class="bi bi-check-circle kpi-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card" style="background: linear-gradient(135deg,#dc3545,#b02a37)">
            <div class="kpi-label">En Camino</div>
            <div class="kpi-value">{{ $kpis['en_camino'] }}</div>
            <i class="bi bi-geo-alt kpi-icon"></i>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kpi-card" style="background: linear-gradient(135deg,#6f42c1,#59359a)">
            <div class="kpi-label">Ingresos Hoy</div>
            <div class="kpi-value">S/ {{ number_format($kpis['ingresos_hoy'], 0) }}</div>
            <i class="bi bi-currency-dollar kpi-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card" style="background: linear-gradient(135deg,#0dcaf0,#0aa2c0)">
            <div class="kpi-label">Ingresos del Mes</div>
            <div class="kpi-value">S/ {{ number_format($kpis['ingresos_mes'], 0) }}</div>
            <i class="bi bi-graph-up kpi-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card" style="background: linear-gradient(135deg,#20c997,#1aa179)">
            <div class="kpi-label">Clientes Activos</div>
            <div class="kpi-value">{{ $kpis['clientes_total'] }}</div>
            <i class="bi bi-people kpi-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card" style="background: linear-gradient(135deg,#fd7e14,#e96b0c)">
            <div class="kpi-label">Repartidores Disp.</div>
            <div class="kpi-value">{{ $kpis['repartidores_disp'] }}</div>
            <i class="bi bi-bicycle kpi-icon"></i>
        </div>
    </div>
</div>

<!-- GRÁFICAS -->
<div class="row g-3 mb-4">
    <!-- Pedidos 7 días -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart me-2 text-primary"></i>Pedidos — Últimos 7 días</span>
            </div>
            <div class="card-body">
                <canvas id="chartPedidos" height="110"></canvas>
            </div>
        </div>
    </div>
    <!-- Distribución de estados -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2 text-primary"></i>Estado de Pedidos
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartEstados" style="max-height:200px"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- TABLAS INFERIORES -->
<div class="row g-3">
    <!-- Últimos pedidos -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-list-check me-2 text-primary"></i>Últimos Pedidos</span>
                <a href="{{ route('pedidos.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">N° Pedido</th>
                                <th>Cliente</th>
                                <th>Repartidor</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($ultimosPedidos as $pedido)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('pedidos.show', $pedido) }}" class="fw-semibold text-decoration-none">{{ $pedido->numero }}</a>
                                </td>
                                <td>{{ $pedido->cliente?->nombre_completo ?? '—' }}</td>
                                <td>{{ $pedido->repartidor?->nombre ?? '—' }}</td>
                                <td><strong>S/ {{ number_format($pedido->total, 2) }}</strong></td>
                                <td><span class="badge bg-{{ $pedido->estado_badge }}">{{ $pedido->estado_texto }}</span></td>
                                <td class="text-muted small">{{ $pedido->created_at->format('H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No hay pedidos hoy</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top repartidores -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-trophy me-2 text-warning"></i>Top Repartidores (mes)</span>
                <a href="{{ route('repartidores.index') }}" class="btn btn-sm btn-outline-secondary">Ver todos</a>
            </div>
            <div class="card-body">
                @forelse($topRepartidores as $i => $rep)
                <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                    <span class="badge bg-{{ ['warning','secondary','dark'][min($i,2)] }} me-2">{{ $i+1 }}</span>
                    <img src="{{ $rep->foto_url }}" class="avatar-sm me-2" alt="">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $rep->nombre_completo }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">{{ $rep->zona_asignada }}</div>
                    </div>
                    <span class="badge bg-success">{{ $rep->entregas_count }} entregas</span>
                </div>
                @empty
                <p class="text-muted text-center small py-3">Sin datos este mes</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if(!empty($stockBajo) && $stockBajo->count() > 0)
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning-subtle d-flex justify-content-between align-items-center">
                <span><i class="bi bi-exclamation-triangle text-warning me-2"></i><strong>Alertas de stock</strong></span>
                <a href="{{ route('inventario.index', ['filtro' => 'bajo']) }}" class="btn btn-sm btn-outline-warning">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th class="ps-3">Producto</th><th>Categoría</th><th class="text-center">Stock actual</th><th class="text-center">Mínimo</th><th class="text-end pe-3">Acción</th></tr></thead>
                    <tbody>
                    @foreach($stockBajo as $p)
                        <tr>
                            <td class="ps-3">{{ $p->nombre }}</td>
                            <td><small class="text-muted">{{ $p->categoria->nombre ?? '—' }}</small></td>
                            <td class="text-center"><span class="badge bg-{{ $p->stock == 0 ? 'danger' : 'warning' }}">{{ $p->stock }}</span></td>
                            <td class="text-center text-muted">{{ $p->stock_minimo }}</td>
                            <td class="text-end pe-3"><a href="{{ route('inventario.kardex', $p) }}" class="btn btn-sm btn-outline-primary">Reponer</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Gráfica de pedidos 7 días
const ctx1 = document.getElementById('chartPedidos').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: {!! json_encode($labels7dias) !!},
        datasets: [{
            label: 'Pedidos',
            data: {!! json_encode($pedidos7dias) !!},
            backgroundColor: 'rgba(13,110,253,0.7)',
            borderRadius: 6,
        }, {
            label: 'Ingresos (S/)',
            data: {!! json_encode($ingresos7dias) !!},
            type: 'line',
            borderColor: '#198754',
            backgroundColor: 'rgba(25,135,84,0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            yAxisID: 'y2',
        }]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index' },
        scales: {
            y:  { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            y2: { position: 'right', beginAtZero: true, grid: { display: false } }
        },
        plugins: { legend: { position: 'top' } }
    }
});

// Gráfica de estados
const estadosData = {!! json_encode($estadosPedidos) !!};
const ctx2 = document.getElementById('chartEstados').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: Object.keys(estadosData).map(s => s.replace(/_/g,' ').toUpperCase()),
        datasets: [{
            data: Object.values(estadosData),
            backgroundColor: ['#ffc107','#0dcaf0','#0d6efd','#6c757d','#fd7e14','#198754','#dc3545','#343a40'],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});
</script>
@endpush
