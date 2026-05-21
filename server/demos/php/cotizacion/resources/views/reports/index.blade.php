<x-app-layout>
    <x-slot name="title">Reportes</x-slot>
    <x-slot name="actions">
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="{{ route('reports.export.excel', request()->query()) }}" class="btn btn-secondary" title="Exportar a Excel">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-secondary" title="Exportar a PDF">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 9h1m4 0h1m-5 4h5m-5 4h5"/></svg>
                PDF
            </a>
            <button onclick="window.print()" class="btn btn-secondary" title="Imprimir">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir
            </button>

            <form method="GET" style="display:flex;gap:8px;align-items:center;">
                <label style="font-size:12px;font-weight:600;color:var(--text-muted);">Año:</label>
                <select name="year" class="form-control" style="width:100px" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    @php
        $jsMonthly  = json_encode($monthly);
        $jsLabels   = json_encode($monthLabels);
        $jsStatus   = json_encode($byStatus->pluck('qty','status'));
        $jsStatusAmt= json_encode($byStatus->pluck('amount','status'));
    @endphp

    {{-- KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#ebf8ff;">
                <svg style="color:#2b6cb0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div><div class="stat-label">Emitidas</div><div class="stat-value">{{ $totalQuotations }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fff4;">
                <svg style="color:#276749" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><div class="stat-label">Aprobadas</div><div class="stat-value">{{ $totalApproved }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fffbeb;">
                <svg style="color:#92400e" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><div class="stat-label">Ingresos ({{ $globalSym }})</div><div class="stat-value" style="font-size:16px">{{ number_format($totalRevenue,0) }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e0f2fe;">
                <svg style="color:#0369a1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div><div class="stat-label">Ticket Promedio</div><div class="stat-value" style="font-size:15px">{{ $globalSym }} {{ number_format($avgTicket,0) }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff5f5;">
                <svg style="color:#c53030" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div><div class="stat-label">Tasa Aprobación</div><div class="stat-value" style="color:var(--success)">{{ $conversionRate }}%</div></div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:16px;">
        {{-- Monthly Revenue Chart --}}
        <div class="card">
            <div class="card-header">
                <div><div class="card-title">Ingresos Mensuales {{ $year }}</div><div class="card-sub">Total aprobado por mes</div></div>
            </div>
            <canvas id="monthlyChart" height="90"></canvas>
        </div>

        {{-- Status Donut --}}
        <div class="card">
            <div class="card-header"><div class="card-title">Distribución por Estado</div></div>
            <canvas id="statusDonut" height="150"></canvas>
            <div style="margin-top:14px;display:flex;flex-direction:column;gap:8px;">
                @foreach($byStatus as $s)
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:12.5px;">
                    <span class="badge @if($s->status==='Aprobada') badge-green @elseif($s->status==='Rechazada') badge-red @elseif($s->status==='Emitida') badge-blue @else badge-gray @endif">{{ $s->status }}</span>
                    <div style="display:flex;gap:14px;">
                        <span style="font-weight:700">{{ $s->qty }}</span>
                        <span style="color:var(--text-muted)">{{ $globalSym }} {{ number_format($s->amount,0) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        {{-- Top Clients --}}
        <div class="card">
            <div class="card-header"><div class="card-title">Top 5 Clientes (Aprobadas)</div></div>
            @if($topClients->isEmpty())
                <p style="color:var(--text-muted);font-size:13px;text-align:center;padding:20px">Sin datos aún</p>
            @else
            <div style="display:flex;flex-direction:column;gap:14px;">
                @foreach($topClients as $i => $tc)
                @php $maxAmt = $topClients->first()->total_amount ?: 1; $pct = round($tc->total_amount/$maxAmt*100); @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:12.5px;font-weight:600">{{ $tc->client->name ?? 'N/A' }}</span>
                        <span style="font-size:12.5px;font-weight:700;color:var(--success)">{{ $globalSym }} {{ number_format($tc->total_amount,0) }}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:{{ ['#4ade80','#38bdf8','#fb923c','#a78bfa','#f472b6'][$i] }};"></div>
                    </div>
                    <div style="font-size:10.5px;color:var(--text-muted);margin-top:3px">{{ $tc->qty }} cotizaciones aprobadas</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- By Currency --}}
        <div class="card">
            <div class="card-header"><div class="card-title">Por Moneda</div></div>
            <canvas id="currencyBar" height="180"></canvas>
            <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px;">
                @foreach($byCurrency as $c)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:#f8fafc;border-radius:8px;font-size:12.5px;">
                    <span style="font-weight:700;font-size:14px">{{ $c->currency }}</span>
                    <span style="color:var(--text-muted)">{{ $c->qty }} cotizaciones</span>
                    <span style="font-weight:700;color:var(--success)">{{ number_format($c->amount,2) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
    const monthlyData = {!! $jsMonthly !!};
    const monthLabels = {!! $jsLabels !!};
    const byStatus    = {!! $jsStatus !!};
    const byStatusAmt = {!! $jsStatusAmt !!};

    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size   = 11;

    // Monthly bar chart
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Ingresos {{ $globalSym }}',
                data: monthlyData,
                backgroundColor: monthlyData.map(() => 'rgba(74,222,128,.7)'),
                borderColor: '#4ade80',
                borderWidth: 1,
                borderRadius: 6, borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: '#f0f4f8' }, beginAtZero: true, ticks: { callback: v => '{{ $globalSym }} '+v.toLocaleString() } }
            }
        }
    });

    // Status donut
    const statusKeys = Object.keys(byStatus);
    const statusVals = statusKeys.map(k => byStatus[k] || 0);
    const statusColors = { 'Borrador':'#cbd5e1','Emitida':'#38bdf8','Aprobada':'#4ade80','Rechazada':'#f87171' };
    new Chart(document.getElementById('statusDonut'), {
        type: 'doughnut',
        data: {
            labels: statusKeys,
            datasets: [{ data: statusVals, backgroundColor: statusKeys.map(k => statusColors[k] || '#ccc'), borderWidth: 0, hoverOffset: 5 }]
        },
        options: { responsive: true, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10 } } } }
    });

    // Currency bar
    const byCur = @json($byCurrency->pluck('qty','currency'));
    new Chart(document.getElementById('currencyBar'), {
        type: 'bar',
        data: {
            labels: Object.keys(byCur),
            datasets: [{ data: Object.values(byCur), backgroundColor: ['#4ade80','#38bdf8','#fb923c'], borderRadius: 8, borderSkipped: false }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f0f4f8' } } }
        }
    });
    </script>
</x-app-layout>
