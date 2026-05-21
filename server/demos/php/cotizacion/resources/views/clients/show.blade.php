<x-app-layout>
    <x-slot name="title">{{ $client->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-secondary">Editar Cliente</a>
        <a href="{{ route('quotations.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nueva Cotización
        </a>
    </x-slot>

    {{-- Header del cliente --}}
    <div class="card" style="margin-bottom:16px;padding:24px 28px;">
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div style="width:64px;height:64px;border-radius:14px;background:linear-gradient(135deg,#4ade80,#38bdf8);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#1e2d3d;flex-shrink:0;">
                {{ strtoupper(substr($client->name,0,1)) }}
            </div>
            <div style="flex:1;">
                <h1 style="font-size:20px;font-weight:800;color:var(--text-main);margin-bottom:4px;">{{ $client->name }}</h1>
                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    @if($client->document_number)
                    <span style="font-size:12.5px;color:var(--text-muted);">
                        <strong>RUC/NIT:</strong> {{ $client->document_number }}
                    </span>
                    @endif
                    @if($client->email)
                    <a href="mailto:{{ $client->email }}" style="font-size:12.5px;color:#2b6cb0;text-decoration:none;">
                        ✉ {{ $client->email }}
                    </a>
                    @endif
                    @if($client->phone)
                    <span style="font-size:12.5px;color:var(--text-muted);">📞 {{ $client->phone }}</span>
                    @endif
                    @if($client->address)
                    <span style="font-size:12.5px;color:var(--text-muted);">📍 {{ $client->address }}</span>
                    @endif
                </div>
                @if($client->notes)
                <div style="margin-top:8px;font-size:12px;color:var(--text-muted);font-style:italic;">{{ $client->notes }}</div>
                @endif
            </div>
            <div style="text-align:right;">
                <div style="font-size:11px;color:var(--text-muted);">Cliente desde</div>
                <div style="font-weight:700;font-size:14px;">{{ $client->created_at->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:16px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#ebf8ff;">
                <svg style="color:#2b6cb0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div><div class="stat-label">Cotizaciones</div><div class="stat-value">{{ $totalQuotations }}</div></div>
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
            <div><div class="stat-label">Ingresos</div><div class="stat-value" style="font-size:16px">{{ $globalSym }} {{ number_format($totalRevenue,0) }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e0f7fa;">
                <svg style="color:#00838f" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div><div class="stat-label">Conversión</div><div class="stat-value" style="color:var(--success)">{{ $conversionRate }}%</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff5f5;">
                <svg style="color:#c53030" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="stat-label">Última Cotiz.</div>
                <div class="stat-value" style="font-size:13px">{{ $lastQuotation ? $lastQuotation->issue_date->format('d/m/Y') : '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Quotations table --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Historial de Cotizaciones</div>
            <div style="display:flex;gap:8px;">
                @foreach(['Borrador'=>'badge-gray','Emitida'=>'badge-blue','Aprobada'=>'badge-green','Rechazada'=>'badge-red'] as $st => $cls)
                    @if(isset($byStatus[$st]))
                        <span class="badge {{ $cls }}">{{ $st }}: {{ $byStatus[$st] }}</span>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Emisión</th>
                        <th>Vencimiento</th>
                        <th>Moneda</th>
                        <th style="text-align:right">Total</th>
                        <th>Estado</th>
                        <th style="text-align:center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $q)
                    <tr>
                        <td>
                            <a href="{{ route('quotations.show',$q) }}" style="font-weight:700;color:#2b6cb0;text-decoration:none;">{{ $q->quotation_number }}</a>
                        </td>
                        <td style="color:var(--text-muted);font-size:12.5px">{{ $q->issue_date->format('d/m/Y') }}</td>
                        <td style="font-size:12.5px">
                            @if($q->due_date)
                                <span style="color:{{ ($q->due_date->isPast() && $q->status === 'Emitida') ? '#f56565' : 'var(--text-muted)' }}">
                                    {{ $q->due_date->format('d/m/Y') }}{{ ($q->due_date->isPast() && $q->status === 'Emitida') ? ' ⚠' : '' }}
                                </span>
                            @else
                                <span style="color:var(--text-muted)">—</span>
                            @endif
                        </td>
                        <td><span class="badge badge-cyan">{{ $q->currency }}</span></td>
                        <td style="text-align:right;font-weight:700">{{ $q->currency_symbol }} {{ number_format($q->total,2) }}</td>
                        <td><span class="badge {{ $q->status_color }}">{{ $q->status }}</span></td>
                        <td style="text-align:center">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="{{ route('quotations.show',$q) }}" class="btn btn-secondary btn-sm">Ver</a>
                                <a href="{{ route('quotations.pdf',$q) }}" class="btn btn-info btn-sm" target="_blank">PDF</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">No hay cotizaciones para este cliente.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
