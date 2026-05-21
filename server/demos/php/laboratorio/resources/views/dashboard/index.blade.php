@extends('layouts.app')

@section('title', 'Dashboard Gerencial')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Panel de Control</h1>
        <p class="text-secondary" style="font-size:0.9rem;">
            <i class="fa-solid fa-circle" style="color:var(--success);font-size:0.5rem;vertical-align:middle;margin-right:6px;"></i>
            Sistema activo — {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>
    <a href="{{ route('reportes.caja_diaria') }}" target="_blank" class="btn btn-primary">
        <i class="fa-solid fa-download"></i> Exportar Reporte de Caja
    </a>
</div>

<div class="dashboard-grid">

    {{-- ── KPI: Órdenes Hoy ─────────────────────────────── --}}
    <div class="col-3">
        <div class="card kpi-blue" style="padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
                <div class="card-icon">
                    <i class="fa-solid fa-file-medical"></i>
                </div>
                <span style="font-size:0.72rem;background:rgba(0,180,216,0.15);color:var(--info);padding:3px 8px;border-radius:20px;border:1px solid rgba(0,180,216,0.25);">Hoy</span>
            </div>
            <div class="card-value">{{ $ordenesHoy }}</div>
            <div style="font-size:0.82rem;color:var(--text-secondary);margin-bottom:4px;">Órdenes Generadas</div>
            <div style="font-size:0.78rem;color:var(--success);"><i class="fa-solid fa-arrow-trend-up"></i> +12.5% vs ayer</div>
        </div>
    </div>

    {{-- ── KPI: Pacientes ───────────────────────────────── --}}
    <div class="col-3">
        <div class="card kpi-teal" style="padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
                <div class="card-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <span style="font-size:0.72rem;background:rgba(0,207,200,0.12);color:var(--accent-teal);padding:3px 8px;border-radius:20px;border:1px solid rgba(0,207,200,0.25);">Total</span>
            </div>
            <div class="card-value">{{ $pacientesTotal }}</div>
            <div style="font-size:0.82rem;color:var(--text-secondary);margin-bottom:4px;">Pacientes Registrados</div>
            <div style="font-size:0.78rem;color:var(--success);"><i class="fa-solid fa-user-plus"></i> +3 nuevos esta semana</div>
        </div>
    </div>

    {{-- ── KPI: Ingresos ────────────────────────────────── --}}
    <div class="col-3">
        <div class="card kpi-green" style="padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
                <div class="card-icon">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
                <span style="font-size:0.72rem;background:rgba(6,214,160,0.12);color:var(--success);padding:3px 8px;border-radius:20px;border:1px solid rgba(6,214,160,0.25);">S/</span>
            </div>
            <div class="card-value">{{ number_format($ingresosHoy, 2) }}</div>
            <div style="font-size:0.82rem;color:var(--text-secondary);margin-bottom:4px;">Ingresos del Día</div>
            <div style="font-size:0.78rem;color:var(--success);"><i class="fa-solid fa-arrow-trend-up"></i> +8.2% vs ayer</div>
        </div>
    </div>

    {{-- ── KPI: Pendientes ─────────────────────────────── --}}
    <div class="col-3">
        <div class="card kpi-amber" style="padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
                <div class="card-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <span style="font-size:0.72rem;background:rgba(255,209,102,0.12);color:var(--warning);padding:3px 8px;border-radius:20px;border:1px solid rgba(255,209,102,0.25);">Activas</span>
            </div>
            <div class="card-value">{{ $ordenesPendientes }}</div>
            <div style="font-size:0.82rem;color:var(--text-secondary);margin-bottom:4px;">Órdenes Pendientes</div>
            <div style="font-size:0.78rem;color:var(--warning);"><i class="fa-solid fa-circle-exclamation"></i> Requieren atención</div>
        </div>
    </div>

    {{-- ── Gráfico de Barras: Órdenes por mes ──────────── --}}
    <div class="col-8">
        <div class="card chart-panel" style="height:100%;">
            <div class="card-header" style="border-color:rgba(0,180,216,0.12);">
                <div>
                    <span class="card-title" style="color:var(--text-primary);font-size:1rem;">
                        <i class="fa-solid fa-chart-column" style="color:var(--accent-primary);margin-right:8px;"></i>
                        Órdenes Generadas
                    </span>
                    <p style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">Últimos 6 meses</p>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="width:10px;height:10px;border-radius:2px;background:var(--gradient-primary);display:inline-block;"></span>
                    <span style="font-size:0.8rem;color:var(--text-muted);">Órdenes</span>
                </div>
            </div>
            <div style="height:300px;position:relative;">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Gráfico de Dona: Estados ─────────────────────── --}}
    <div class="col-4">
        <div class="card chart-panel" style="height:100%;">
            <div class="card-header" style="border-color:rgba(0,180,216,0.12);">
                <div>
                    <span class="card-title" style="color:var(--text-primary);font-size:1rem;">
                        <i class="fa-solid fa-chart-pie" style="color:var(--accent-primary);margin-right:8px;"></i>
                        Estado de Órdenes
                    </span>
                    <p style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">Mes actual</p>
                </div>
            </div>
            <div style="height:260px;position:relative;display:flex;justify-content:center;align-items:center;">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Gráfico de Área: Ingresos por Mes ──────────── --}}
    <div class="col-6">
        <div class="card chart-panel" style="height:100%;">
            <div class="card-header" style="border-color:rgba(0,180,216,0.12);">
                <div>
                    <span class="card-title" style="color:var(--text-primary);font-size:1rem;">
                        <i class="fa-solid fa-money-bill-trend-up" style="color:var(--success);margin-right:8px;"></i>
                        Tendencia de Ingresos
                    </span>
                    <p style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">Últimos 6 meses (S/)</p>
                </div>
            </div>
            <div style="height:280px;position:relative;">
                <canvas id="areaChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Gráfico Horizontal: Top Pruebas ────────────── --}}
    <div class="col-6">
        <div class="card chart-panel" style="height:100%;">
            <div class="card-header" style="border-color:rgba(0,180,216,0.12);">
                <div>
                    <span class="card-title" style="color:var(--text-primary);font-size:1rem;">
                        <i class="fa-solid fa-flask" style="color:var(--accent-primary);margin-right:8px;"></i>
                        Pruebas Más Frecuentes
                    </span>
                    <p style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">Ranking General</p>
                </div>
            </div>
            <div style="height:280px;position:relative;">
                <canvas id="horizontalBarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Tabla Órdenes Recientes ──────────────────────── --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div>
                    <span class="card-title" style="color:var(--text-primary);font-size:1rem;">
                        <i class="fa-solid fa-list-ul" style="color:var(--accent-primary);margin-right:8px;"></i>
                        Órdenes Recientes
                    </span>
                    <p style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">Últimas 5 órdenes registradas</p>
                </div>
                <a href="{{ route('ordenes.index') }}" class="btn btn-secondary" style="font-size:0.8rem;padding:7px 14px;">
                    Ver Todas <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nro Orden</th>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenesRecientes as $orden)
                        <tr>
                            <td>
                                <code>{{ $orden->numero_orden }}</code>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:30px;height:30px;border-radius:50%;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;flex-shrink:0;">
                                        {{ substr($orden->paciente->nombres ?? 'X', 0, 1) }}
                                    </div>
                                    <span>{{ $orden->paciente->nombre_completo ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>{{ $orden->fecha_registro ? $orden->fecha_registro->format('d/m/Y H:i') : ($orden->created_at ? $orden->created_at->format('d/m/Y H:i') : 'N/A') }}</td>
                            <td><strong style="color:var(--success);">S/ {{ number_format($orden->total, 2) }}</strong></td>
                            <td>
                                @if(in_array($orden->estado, ['Completado','Entregado']))
                                    <span class="status-badge status-completed">{{ $orden->estado }}</span>
                                @elseif(in_array($orden->estado, ['Pendiente','En proceso']))
                                    <span class="status-badge status-pending">{{ $orden->estado }}</span>
                                @else
                                    <span class="status-badge status-critical">{{ $orden->estado }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ordenes.show', $orden) }}" class="action-btn" title="Ver detalle">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(in_array($orden->estado, ['Completado','Entregado']))
                                <a href="{{ route('resultados.pdf', $orden) }}" target="_blank" class="action-btn" title="Descargar PDF" style="color:var(--success);">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding:40px;">
                                <i class="fa-solid fa-inbox" style="font-size:2rem;display:block;margin-bottom:8px;opacity:0.3;"></i>
                                No hay órdenes recientes.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // ── Paleta clínica (Tema Claro)
    const colorTeal    = '#0d9488';
    const colorBlue    = '#0284c7';
    const colorCyan    = '#0ea5e9';
    const colorSuccess = '#10b981';
    const colorWarning = '#f59e0b';
    const colorDanger  = '#ef4444';
    const colorPurple  = '#8b5cf6';
    const textColor    = '#64748b'; // texto secundario/muted para labels
    const gridColor    = '#f1f5f9'; // borde muy suave

    // ── Gradiente para barras
    function makeGradient(ctx, color1, color2) {
        const g = ctx.createLinearGradient(0, 0, 0, 350);
        g.addColorStop(0, color1);
        g.addColorStop(1, color2 + '20'); // muy transparente abajo
        return g;
    }

    // ── Gráfico de barras
    const ctxBar = document.getElementById('barChart').getContext('2d');
    const barGrad = makeGradient(ctxBar, colorTeal, colorBlue);

    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: {!! json_encode($meses) !!},
            datasets: [{
                label: 'Órdenes',
                data: {!! json_encode($ordenesPorMes) !!},
                backgroundColor: barGrad,
                borderColor: colorBlue,
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 32,
                hoverBackgroundColor: colorCyan,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#ffffff',
                    titleColor: '#0f172a',
                    bodyColor: '#475569',
                    borderColor: '#cbd5e1',
                    borderWidth: 1,
                    padding: 10,
                    boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} órdenes`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor, drawBorder: false },
                    ticks: { color: textColor, font: { size: 11 }, stepSize: 1 },
                    border: { display: false }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: textColor, font: { size: 11 } },
                    border: { display: false }
                }
            },
            animation: {
                duration: 900,
                easing: 'easeOutQuart'
            }
        }
    });

    // ── Gráfico de dona
    const estados = {!! json_encode($estados) !!};
    const doughnutLabels = Object.keys(estados);
    const doughnutData   = Object.values(estados);
    const palette = [colorSuccess, colorWarning, colorDanger, colorTeal, colorPurple, colorBlue];

    const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: doughnutLabels,
            datasets: [{
                data: doughnutData,
                backgroundColor: palette.slice(0, doughnutLabels.length),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverBorderColor: '#f8fafc',
                hoverOffset: 4,
                spacing: 1,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: textColor, padding: 14, usePointStyle: true, pointStyleWidth: 8, font: { size: 11 } }
                },
                tooltip: {
                    backgroundColor: '#ffffff', titleColor: '#0f172a', bodyColor: '#475569', borderColor: '#cbd5e1', borderWidth: 1, padding: 10,
                }
            },
            animation: { duration: 900, easing: 'easeOutQuart' }
        }
    });

    // ── Gráfico de Área (Ingresos)
    const ctxArea = document.getElementById('areaChart').getContext('2d');
    const areaGrad = ctxArea.createLinearGradient(0, 0, 0, 300);
    areaGrad.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); // Success green pastel
    areaGrad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    new Chart(ctxArea, {
        type: 'line',
        data: {
            labels: {!! json_encode($meses) !!},
            datasets: [{
                label: 'Ingresos (S/)',
                data: {!! json_encode($ingresosPorMes) !!},
                borderColor: colorSuccess,
                backgroundColor: areaGrad,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: colorSuccess,
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Curvas suaves
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#ffffff', titleColor: '#0f172a', bodyColor: '#475569', borderColor: '#cbd5e1', borderWidth: 1, padding: 10, boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                    callbacks: { label: ctx => ` S/ ${ctx.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2})}` }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { color: textColor, font: { size: 11 } }, border: { display: false } },
                x: { grid: { display: false, drawBorder: false }, ticks: { color: textColor, font: { size: 11 } }, border: { display: false } }
            },
            animation: { duration: 1000, easing: 'easeOutQuart' }
        }
    });

    // ── Gráfico de Barras Horizontales (Top Pruebas)
    const topPruebas = {!! json_encode($topPruebas) !!};
    const ctxHBar = document.getElementById('horizontalBarChart').getContext('2d');

    new Chart(ctxHBar, {
        type: 'bar',
        data: {
            labels: Object.keys(topPruebas),
            datasets: [{
                label: 'Frecuencia',
                data: Object.values(topPruebas),
                backgroundColor: 'rgba(14, 165, 233, 0.2)', // Sky blue soft
                borderColor: colorCyan,
                borderWidth: 1,
                borderRadius: 4,
                hoverBackgroundColor: 'rgba(14, 165, 233, 0.4)'
            }]
        },
        options: {
            indexAxis: 'y', // La vuelve horizontal
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#ffffff', titleColor: '#0f172a', bodyColor: '#475569', borderColor: '#cbd5e1', borderWidth: 1, padding: 10 }
            },
            scales: {
                x: { beginAtZero: true, grid: { display: false }, ticks: { color: textColor, font: { size: 11 }, stepSize: 1 }, border: { display: false } },
                y: { grid: { display: false }, ticks: { color: textColor, font: { size: 11 } }, border: { display: false } }
            },
            animation: { duration: 1100, easing: 'easeOutQuart' }
        }
    });
</script>
@endpush
