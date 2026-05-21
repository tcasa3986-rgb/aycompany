@extends('layouts.app')
@section('title', 'Inventario de Reactivos')
@section('content')
<div class="page-header">
    <div><h1 class="page-title text-gradient">Inventario de Reactivos</h1><p class="text-secondary">Control de stock y vencimiento de reactivos del laboratorio</p></div>
    <a href="{{ route('reactivos.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo Reactivo</a>
</div>

@if(session('success'))
    <div style="background:rgba(46,213,115,0.12);border-left:4px solid var(--success);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--success);"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:rgba(255,71,87,0.12);border-left:4px solid var(--danger);padding:12px 16px;border-radius:8px;margin-bottom:1rem;color:var(--danger);"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
@endif

{{-- Alertas de stock --}}
@if($stockBajoCount > 0 || $sinStockCount > 0 || $vencidosCount > 0)
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;">
    @if($sinStockCount > 0)
    <div style="background:rgba(255,71,87,0.1);border:1px solid rgba(255,71,87,0.3);border-radius:10px;padding:1rem;display:flex;align-items:center;gap:12px;">
        <i class="fa-solid fa-circle-exclamation" style="font-size:1.5rem;color:var(--danger);"></i>
        <div><strong style="color:var(--danger);">{{ $sinStockCount }} Sin stock</strong><br><small class="text-muted">Requieren reposición urgente</small></div>
    </div>
    @endif
    @if($stockBajoCount > 0)
    <div style="background:rgba(255,177,66,0.1);border:1px solid rgba(255,177,66,0.3);border-radius:10px;padding:1rem;display:flex;align-items:center;gap:12px;">
        <i class="fa-solid fa-triangle-exclamation" style="font-size:1.5rem;color:var(--warning);"></i>
        <div><strong style="color:var(--warning);">{{ $stockBajoCount }} Stock bajo</strong><br><small class="text-muted">Por debajo del mínimo</small></div>
    </div>
    @endif
    @if($vencidosCount > 0)
    <div style="background:rgba(100,100,100,0.1);border:1px solid rgba(100,100,100,0.3);border-radius:10px;padding:1rem;display:flex;align-items:center;gap:12px;">
        <i class="fa-solid fa-calendar-xmark" style="font-size:1.5rem;color:var(--text-muted);"></i>
        <div><strong>{{ $vencidosCount }} Vencidos</strong><br><small class="text-muted">Fecha de vencimiento pasada</small></div>
    </div>
    @endif
</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Lista de Reactivos</span>
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar reactivo..." style="background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;width:200px;">
            <select name="area_id" style="background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;">
                <option value="">Todas las áreas</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                @endforeach
            </select>
            <select name="estado" style="background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;">
                <option value="">Todos los estados</option>
                @foreach(['Disponible','Stock bajo','Sin stock','Vencido'] as $e)
                    <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ $e }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-filter"></i></button>
            @if(request()->hasAny(['search','area_id','estado']))
                <a href="{{ route('reactivos.index') }}" class="btn btn-secondary">Limpiar</a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>Código</th><th>Reactivo</th><th>Área</th><th>Stock Actual</th><th>Stock Mínimo</th><th>Vencimiento</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($reactivos as $r)
                <tr>
                    <td><code style="color:var(--accent-primary);">{{ $r->codigo }}</code></td>
                    <td>
                        <strong>{{ $r->nombre }}</strong>
                        @if($r->marca)<br><small class="text-muted">{{ $r->marca }}</small>@endif
                    </td>
                    <td>{{ $r->area->nombre ?? '—' }}</td>
                    <td>
                        <strong style="color:{{ $r->stock_actual <= 0 ? 'var(--danger)' : ($r->stock_actual <= $r->stock_minimo ? 'var(--warning)' : 'var(--success)') }};">
                            {{ $r->stock_actual }}
                        </strong>
                        <small class="text-muted">{{ $r->unidad_medida }}</small>
                    </td>
                    <td>{{ $r->stock_minimo }}</td>
                    <td>
                        @if($r->fecha_vencimiento)
                            <span style="color:{{ $r->fecha_vencimiento->isPast() ? 'var(--danger)' : ($r->fecha_vencimiento->diffInDays() < 30 ? 'var(--warning)' : 'inherit') }};">
                                {{ $r->fecha_vencimiento->format('d/m/Y') }}
                            </span>
                        @else —
                        @endif
                    </td>
                    <td>
                        @php $badge = match($r->estado) { 'Disponible' => 'status-completed', 'Stock bajo' => 'status-pending', default => 'status-critical' }; @endphp
                        <span class="status-badge {{ $badge }}">{{ $r->estado }}</span>
                    </td>
                    <td>
                        <button class="action-btn text-success" title="Ajustar stock" onclick="abrirStockModal({{ $r->id }}, '{{ addslashes($r->nombre) }}', {{ $r->stock_actual }})"><i class="fa-solid fa-arrows-up-down"></i></button>
                        <a href="{{ route('reactivos.edit', $r) }}" class="action-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('reactivos.destroy', $r) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn {{ $r->activo ? 'text-danger' : 'text-success' }}" title="{{ $r->activo ? 'Desactivar' : 'Activar' }}" onclick="return confirm('¿Cambiar estado?')">
                                <i class="fa-solid {{ $r->activo ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">No hay reactivos en el inventario.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reactivos->hasPages())<div style="padding:1rem;">{{ $reactivos->links() }}</div>@endif
</div>

{{-- Modal Ajuste de Stock --}}
<div id="stockModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:1000;align-items:center;justify-content:center;">
    <div class="card" style="width:400px;max-width:90vw;">
        <div class="card-header">
            <span class="card-title">Ajustar Stock</span>
            <button onclick="document.getElementById('stockModal').style.display='none'" style="background:none;border:none;color:var(--text);font-size:1.3rem;cursor:pointer;">×</button>
        </div>
        <form id="stockForm" method="POST" style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
            @csrf
            <p id="stockNombre" class="text-secondary" style="margin:0;"></p>
            <p>Stock actual: <strong id="stockActual"></strong></p>
            <div class="form-group">
                <label>Tipo de movimiento</label>
                <select name="tipo" class="form-control" id="stockTipo">
                    <option value="entrada">Entrada (añadir)</option>
                    <option value="salida">Salida (retirar)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Cantidad</label>
                <input type="number" name="cantidad" class="form-control" min="1" value="1" required>
            </div>
            <div class="form-group">
                <label>Motivo (opcional)</label>
                <input type="text" name="motivo" class="form-control" placeholder="Ej: Compra, uso en análisis...">
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('stockModal').style.display='none'" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Registrar</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function abrirStockModal(id, nombre, stockActual) {
    document.getElementById('stockNombre').textContent = nombre;
    document.getElementById('stockActual').textContent = stockActual;
    document.getElementById('stockForm').action = `/reactivos/${id}/stock`;
    document.getElementById('stockModal').style.display = 'flex';
}
</script>
@endpush
