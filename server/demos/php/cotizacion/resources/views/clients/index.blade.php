<x-app-layout>
    <x-slot name="title">Clientes</x-slot>
    <x-slot name="actions">
        <div style="display:flex;gap:8px;">
            <a href="{{ route('clients.export.excel', request()->query()) }}" class="btn btn-secondary" title="Exportar a Excel">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('clients.export.pdf', request()->query()) }}" class="btn btn-secondary" title="Exportar a PDF">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 9h1m4 0h1m-5 4h5m-5 4h5"/></svg>
                PDF
            </a>
            <button onclick="window.print()" class="btn btn-secondary" title="Imprimir">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir
            </button>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nuevo Cliente
            </a>
        </div>
    </x-slot>

    <div class="card">
        <form method="GET" style="display:flex;gap:10px;margin-bottom:18px;align-items:flex-end;">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" placeholder="Nombre, RUC o email..." value="{{ request('search') }}" style="width:280px">
            </div>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            @if(request('search'))<a href="{{ route('clients.index') }}" class="btn btn-secondary">Limpiar</a>@endif
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nombre / Razón Social</th>
                        <th>RUC / DNI</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Cotizaciones</th>
                        <th style="text-align:center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $c)
                    <tr>
                        <td style="font-weight:600">
                            <a href="{{ route('clients.show',$c) }}" style="color:var(--text-main);text-decoration:none;">
                                {{ $c->name }}
                            </a>
                        </td>
                        <td style="color:var(--text-muted);font-size:12.5px">{{ $c->document_number ?: '—' }}</td>
                        <td style="font-size:12.5px">
                            @if($c->email)
                                <a href="mailto:{{ $c->email }}" style="color:#2b6cb0;text-decoration:none;">{{ $c->email }}</a>
                            @else
                                <span style="color:var(--text-muted)">—</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);font-size:12.5px">{{ $c->phone ?: '—' }}</td>
                        <td>
                            <span class="badge badge-blue">{{ $c->quotations_count ?? 0 }}</span>
                        </td>
                        <td style="text-align:center">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="{{ route('clients.edit',$c) }}" class="btn btn-secondary btn-sm">Editar</a>
                                <form method="POST" action="{{ route('clients.destroy',$c) }}" onsubmit="return confirm('¿Eliminar este cliente?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">
                        No hay clientes registrados. <a href="{{ route('clients.create') }}" style="color:#2b6cb0;font-weight:600">Agregar el primero</a>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($clients,'hasPages') && $clients->hasPages())
        <div style="margin-top:16px;">{{ $clients->links() }}</div>
        @endif
    </div>
</x-app-layout>
