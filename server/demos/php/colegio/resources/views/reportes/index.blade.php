@extends('layouts.app')
@section('title', 'Reportes')
@section('page-title', 'Centro de Reportes')

@section('content')

{{-- KPIs principales --}}
<div class="grid grid-4" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-info">
            <div class="stat-value">S/. {{ number_format($resumen['total_ingresos'], 0) }}</div>
            <div class="stat-label">Ingresos {{ $anio }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value">S/. {{ number_format($resumen['total_pendiente'], 0) }}</div>
            <div class="stat-label">Por cobrar</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $resumen['total_alumnos'] }}</div>
            <div class="stat-label">Alumnos activos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-file-signature"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $resumen['total_matriculas'] }}</div>
            <div class="stat-label">Matrículas {{ $anio }}</div>
        </div>
    </div>
</div>

{{-- Accesos rápidos a reportes --}}
<div class="grid grid-3" style="margin-bottom:24px;">

    <div class="card" style="padding:24px;text-align:center;cursor:pointer;transition:transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
        <div style="width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,#065f46,#10b981);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:26px;color:white;">
            <i class="fas fa-receipt"></i>
        </div>
        <div style="font-size:16px;font-weight:700;margin-bottom:6px;">Reporte de Pagos</div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:16px;">Listado completo de pagos por mes y año</div>
        <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('reportes.pagos') }}" class="btn btn-success btn-sm"><i class="fas fa-eye"></i> Ver</a>
            <a href="{{ route('reportes.exportar', 'pagos') }}" class="btn btn-secondary btn-sm"><i class="fas fa-download"></i> CSV</a>
        </div>
    </div>

    <div class="card" style="padding:24px;text-align:center;cursor:pointer;transition:transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
        <div style="width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);display:flex;align-items:center;justify-content:margin:0 auto;font-size:26px;color:white;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-users"></i>
        </div>
        <div style="font-size:16px;font-weight:700;margin-bottom:6px;">Reporte de Alumnos</div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:16px;">Lista de alumnos matriculados por grado</div>
        <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('reportes.alumnos') }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Ver</a>
            <a href="{{ route('reportes.exportar', 'alumnos') }}" class="btn btn-secondary btn-sm"><i class="fas fa-download"></i> CSV</a>
        </div>
    </div>

    <div class="card" style="padding:24px;text-align:center;cursor:pointer;transition:transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
        <div style="width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,#7f1d1d,#ef4444);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:26px;color:white;">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div style="font-size:16px;font-weight:700;margin-bottom:6px;">Reporte de Deudas</div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:16px;">Alumnos con pagos pendientes y montos</div>
        <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('reportes.deudas') }}" class="btn btn-danger btn-sm"><i class="fas fa-eye"></i> Ver</a>
            <a href="{{ route('reportes.exportar', 'deudas') }}" class="btn btn-secondary btn-sm"><i class="fas fa-download"></i> CSV</a>
        </div>
    </div>

</div>

{{-- Gráficas analíticas --}}
<div class="grid grid-2" style="margin-bottom:24px;">

    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-chart-bar" style="color:#3b82f6;margin-right:8px;"></i>Ingresos Mensuales {{ $anio }}</span>
        </div>
        <div class="card-body"><canvas id="chartIngresosMes" height="220"></canvas></div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-chart-pie" style="color:#10b981;margin-right:8px;"></i>Ingresos por Tipo de Concepto</span>
        </div>
        <div class="card-body" style="display:flex;align-items:center;justify-content:center;">
            <canvas id="chartTipo" height="220" style="max-width:300px;"></canvas>
        </div>
    </div>

</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-chart-bar" style="color:#8b5cf6;margin-right:8px;"></i>Distribución de Alumnos por Grado — {{ $anio }}</span>
    </div>
    <div class="card-body"><canvas id="chartAlumnos" height="120"></canvas></div>
</div>

@endsection

@push('scripts')
<script>
const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

// Chart 1: Barras de ingresos por mes
const ingData = @json($ingresosMensuales);
const ingArr  = meses.map((_,i) => {
    const found = ingData.find(d => d.mes == (i+1));
    return found ? parseFloat(found.total) : 0;
});
new Chart(document.getElementById('chartIngresosMes'), {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [{
            label: 'Ingresos (S/.)',
            data: ingArr,
            backgroundColor: ingArr.map((v,i) => i === new Date().getMonth() ? '#1e3a8a' : 'rgba(59,130,246,0.6)'),
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'S/.'+v.toLocaleString() }, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});

// Chart 2: Dona por tipo de concepto
const tipoData   = @json($pagosPorTipo);
const tipoLabels = tipoData.map(d => d.tipo.charAt(0).toUpperCase() + d.tipo.slice(1));
const tipoTotals = tipoData.map(d => parseFloat(d.total));
new Chart(document.getElementById('chartTipo'), {
    type: 'doughnut',
    data: {
        labels: tipoLabels.length ? tipoLabels : ['Sin datos'],
        datasets: [{ data: tipoTotals.length ? tipoTotals : [1], backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { cutout: '60%', plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
});

// Chart 3: Barras horizontales alumnos por grado
const gradoData   = @json($alumnosPorGrado);
const gradoLabels = gradoData.map(g => g.grado ? g.grado.nombre : 'N/A');
const gradoTotals = gradoData.map(g => g.total);
new Chart(document.getElementById('chartAlumnos'), {
    type: 'bar',
    data: {
        labels: gradoLabels.length ? gradoLabels : ['Sin datos'],
        datasets: [{ label: 'Alumnos', data: gradoTotals.length ? gradoTotals : [0], backgroundColor: 'rgba(139,92,246,0.7)', borderRadius: 6, borderSkipped: false }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.05)' } },
            y: { grid: { display: false } }
        }
    }
});
</script>
@endpush
