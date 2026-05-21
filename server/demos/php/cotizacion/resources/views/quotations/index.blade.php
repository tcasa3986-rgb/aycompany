<x-app-layout>
    <x-slot name="title">Cotizaciones</x-slot>
    <x-slot name="actions">
        <div style="display:flex;gap:8px;">
            <a href="{{ route('quotations.export.excel', request()->query()) }}" class="btn btn-secondary" title="Exportar a Excel">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('quotations.export.pdf', request()->query()) }}" class="btn btn-secondary" title="Exportar a PDF">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 9h1m4 0h1m-5 4h5m-5 4h5"/></svg>
                PDF
            </a>
            <button onclick="window.print()" class="btn btn-secondary" title="Imprimir">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir
            </button>
            <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nueva Cotización
            </a>
        </div>
    </x-slot>

    <div class="card">
        {{-- Filtros --}}
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px;align-items:flex-end;">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" placeholder="Número o cliente..." value="{{ request('search') }}" style="width:220px">
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select name="status" class="form-control" style="width:140px">
                    <option value="">Todos</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Moneda</label>
                <select name="currency" class="form-control" style="width:100px">
                    <option value="">Todas</option>
                    <option value="PEN" {{ request('currency') == 'PEN' ? 'selected' : '' }}>PEN</option>
                    <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ request('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            @if(request()->anyFilled(['search','status','currency']))
                <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Limpiar</a>
            @endif
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
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
                            <a href="{{ route('quotations.show',$q) }}" style="font-weight:700;color:#2b6cb0;text-decoration:none;">
                                {{ $q->quotation_number }}
                            </a>
                        </td>
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $q->client->name ?? '—' }}
                        </td>
                        <td style="color:var(--text-muted);font-size:12.5px">{{ $q->issue_date->format('d/m/Y') }}</td>
                        <td style="font-size:12.5px">
                            @if($q->due_date)
                                @if($q->due_date->isPast() && $q->status === 'Emitida')
                                    <span style="color:var(--danger);font-weight:600">{{ $q->due_date->format('d/m/Y') }} ⚠</span>
                                @else
                                    <span style="color:var(--text-muted)">{{ $q->due_date->format('d/m/Y') }}</span>
                                @endif
                            @else
                                <span style="color:var(--text-muted)">—</span>
                            @endif
                        </td>
                        <td><span class="badge badge-cyan">{{ $q->currency }}</span></td>
                        <td style="text-align:right;font-weight:700">
                            {{ $q->currency_symbol }} {{ number_format($q->total,2) }}
                        </td>
                        <td><span class="badge {{ $q->status_color }}">{{ $q->status }}</span></td>
                        <td style="text-align:center">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="{{ route('quotations.show',$q) }}" class="btn btn-secondary btn-sm" title="Ver">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('quotations.edit',$q) }}" class="btn btn-secondary btn-sm" title="Editar">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <a href="{{ route('quotations.pdf',$q) }}" class="btn btn-info btn-sm" target="_blank" title="PDF">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                                <form method="POST" action="{{ route('quotations.destroy',$q) }}" onsubmit="return confirm('¿Eliminar esta cotización?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                        No se encontraron cotizaciones.
                        <a href="{{ route('quotations.create') }}" style="color:#2b6cb0;font-weight:600">Crear la primera</a>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($quotations->hasPages())
        <div style="margin-top:16px;">{{ $quotations->links() }}</div>
        @endif
    </div>
</x-app-layout>
