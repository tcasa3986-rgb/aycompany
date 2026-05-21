<x-app-layout>
    <x-slot name="title">Productos</x-slot>
    <x-slot name="actions">
        <div style="display:flex;gap:8px;">
            <a href="{{ route('products.export.excel', request()->query()) }}" class="btn btn-secondary" title="Exportar a Excel">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('products.export.pdf', request()->query()) }}" class="btn btn-secondary" title="Exportar a PDF">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 9h1m4 0h1m-5 4h5m-5 4h5"/></svg>
                PDF
            </a>
            <button onclick="window.print()" class="btn btn-secondary" title="Imprimir">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir
            </button>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nuevo Producto
            </a>
        </div>
    </x-slot>

    <div class="card">
        <form method="GET" style="display:flex;gap:10px;margin-bottom:18px;align-items:flex-end;">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" placeholder="Nombre del producto..." value="{{ request('search') }}" style="width:280px">
            </div>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            @if(request('search'))<a href="{{ route('products.index') }}" class="btn btn-secondary">Limpiar</a>@endif
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nombre / Descripción</th>
                        <th>Unidad</th>
                        <th style="text-align:right">Precio Unit.</th>
                        <th style="text-align:center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $p->name }}</div>
                            @if($p->description)
                            <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px">{{ Str::limit($p->description,80) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($p->unit)
                                <span class="badge badge-cyan">{{ $p->unit }}</span>
                            @else
                                <span style="color:var(--text-muted)">—</span>
                            @endif
                        </td>
                        <td style="font-weight:600;color:var(--text);text-align:right">
                            {{ $globalSym }} {{ number_format($p->price,2) }}
                        </td>
                        <td style="text-align:center">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="{{ route('products.edit',$p) }}" class="btn btn-secondary btn-sm">Editar</a>
                                <form method="POST" action="{{ route('products.destroy',$p) }}" onsubmit="return confirm('¿Eliminar este producto?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;padding:40px;color:var(--text-muted);">
                        No hay productos registrados. <a href="{{ route('products.create') }}" style="color:#2b6cb0;font-weight:600">Agregar el primero</a>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($products,'hasPages') && $products->hasPages())
        <div style="margin-top:16px;">{{ $products->links() }}</div>
        @endif
    </div>
</x-app-layout>
