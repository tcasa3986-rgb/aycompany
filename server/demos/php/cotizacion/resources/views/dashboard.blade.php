<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- ── Stat cards ── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#ebf8ff;">
                <svg style="color:#2b6cb0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="stat-label">Cotizaciones</div>
                <div class="stat-value">{{ $totalQuotations }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fff4;">
                <svg style="color:#276749" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="stat-label">Clientes</div>
                <div class="stat-value">{{ $totalClients }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fffbeb;">
                <svg style="color:#92400e" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <div class="stat-label">Productos</div>
                <div class="stat-value">{{ $totalProducts }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff5f5;">
                <svg style="color:#e53e3e" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="stat-label">TOTAL ({{ $globalCurrency }})</div>
                <div class="stat-value" style="font-size:17px;">{{ $globalSym }} {{ number_format($totalAmount,2) }}</div>
            </div>
        </div>
    </div>

    {{-- ── Row 1: Line chart + Status list ── --}}
    <div style="display:grid;grid-template-columns:1fr 300px;gap:16px;margin-bottom:16px;">

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Actividad de Cotizaciones</div>
                    <div class="card-sub">Últimos 6 meses — monto total emitido</div>
                </div>
                <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm">+ Nueva</a>
            </div>
            <canvas id="lineChart" height="90"></canvas>
        </div>

        <div class="card">
            <div class="card-header"><div class="card-title">Por Estado</div></div>
            @php
                $statusCfg = [
                    'Borrador'  => ['class'=>'badge-gray',  'color'=>'#a0aec0'],
                    'Emitida'   => ['class'=>'badge-blue',  'color'=>'#4299e1'],
                    'Aprobada'  => ['class'=>'badge-green', 'color'=>'#48bb78'],
                    'Rechazada' => ['class'=>'badge-red',   'color'=>'#f56565'],
                ];
            @endphp
            <div style="display:flex;flex-direction:column;gap:14px;">
                @foreach($statusCfg as $st => $cfg)
                    @php $cnt = $byStatus[$st] ?? 0; $pct = $totalQuotations > 0 ? round($cnt/$totalQuotations*100) : 0; @endphp
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                            <span style="font-size:12px;font-weight:600;color:var(--text-main);">{{ $st }}</span>
                            <span style="font-size:12px;font-weight:700;color:var(--text-main);">{{ $cnt }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $cfg['color'] }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="margin-top:20px;border-top:1px solid var(--border);padding-top:14px;">
                <canvas id="donutChart" height="140"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Row 2: Quotations table + Bar chart ── --}}
    <div style="display:grid;grid-template-columns:1fr 340px;gap:16px;">

        <div class="card">
            <div class="card-header">
                <div class="card-title">Cotizaciones Recientes</div>
                <a href="{{ route('quotations.index') }}" class="btn btn-secondary btn-sm">Ver todas</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Número</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Fecha</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentQuotations as $q)
                        <tr>
                            <td><a href="{{ route('quotations.show',$q) }}" style="color:#2b6cb0;font-weight:600;text-decoration:none;">{{ $q->quotation_number }}</a></td>
                            <td style="color:var(--text-muted)">{{ $q->client->name ?? '—' }}</td>
                            <td style="font-weight:700">{{ $q->currency_symbol }} {{ number_format($q->total,2) }}</td>
                            <td><span class="badge {{ $q->status_color }}">{{ $q->status }}</span></td>
                            <td style="color:var(--text-muted);font-size:12px">{{ $q->issue_date->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:28px;">Sin cotizaciones aún</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><div class="card-title">Volumen por Estado</div></div>
            <canvas id="barChart" height="180"></canvas>
            <div style="margin-top:16px;border-top:1px solid var(--border);padding-top:14px;">
                <a href="{{ route('quotations.create') }}" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nueva Cotización
                </a>
            </div>
        </div>
    </div>

    @php
        $jsStatus   = json_encode($byStatus);
        $jsMonthly  = json_encode($monthlyData ?? [0,0,0,0,0,0]);
        $jsLabels   = json_encode($monthLabels  ?? ['Nov','Dic','Ene','Feb','Mar','Abr']);
    @endphp
    <script>
    const byStatus    = {!! $jsStatus !!};
    const monthlyData = {!! $jsMonthly !!};
    const monthLabels = {!! $jsLabels !!};

    const colors = {
        green:  '#4ade80', blue: '#38bdf8', orange: '#fb923c',
        red: '#f87171', gray: '#cbd5e1', teal: '#2dd4bf',
    };

    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size   = 11;

    // ── Line chart ────────────────────────────────────────
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [
                {
                    label: 'Total Emitido',
                    data: monthlyData,
                    borderColor: colors.blue,
                    backgroundColor: 'rgba(56,189,248,.08)',
                    borderWidth: 2.5, fill: true, tension: .4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: colors.blue,
                    pointBorderWidth: 2, pointRadius: 4,
                },
                {
                    label: 'Aprobadas',
                    data: monthlyData.map(v => Math.round(v * 0.6)),
                    borderColor: colors.green,
                    backgroundColor: 'rgba(74,222,128,.06)',
                    borderWidth: 2.5, fill: true, tension: .4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: colors.green,
                    pointBorderWidth: 2, pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top', labels: { boxWidth: 10, padding: 16 } } },
            scales: {
                x: { grid: { color: '#f0f4f8' } },
                y: { grid: { color: '#f0f4f8' }, beginAtZero: true,
                     ticks: { callback: v => '{{ $globalSym }} ' + v.toLocaleString() } }
            }
        }
    });

    // ── Donut chart ───────────────────────────────────────
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Borrador','Emitida','Aprobada','Rechazada'],
            datasets: [{
                data: [
                    byStatus['Borrador']  || 0,
                    byStatus['Emitida']   || 0,
                    byStatus['Aprobada']  || 0,
                    byStatus['Rechazada'] || 0,
                ],
                backgroundColor: [colors.gray, colors.blue, colors.green, colors.red],
                borderWidth: 0, hoverOffset: 6,
            }]
        },
        options: {
            responsive: true, cutout: '68%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 10 } } }
            }
        }
    });

    // ── Bar chart ─────────────────────────────────────────
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: ['Borrador','Emitida','Aprobada','Rechazada'],
            datasets: [{
                data: [
                    byStatus['Borrador']  || 0,
                    byStatus['Emitida']   || 0,
                    byStatus['Aprobada']  || 0,
                    byStatus['Rechazada'] || 0,
                ],
                backgroundColor: [colors.gray, colors.blue, colors.green, colors.red],
                borderRadius: 6, borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: '#f0f4f8' }, beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
    </script>
</x-app-layout>
