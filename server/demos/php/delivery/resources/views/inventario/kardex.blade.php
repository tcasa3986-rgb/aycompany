@extends('layouts.app')
@section('title', 'Kardex: ' . $producto->nombre)

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('inventario.index') }}">Inventario</a></li>
        <li class="breadcrumb-item active">Kardex {{ $producto->nombre }}</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-clipboard-data me-2 text-info"></i>Kardex — {{ $producto->nombre }}</h4>
    <a href="{{ route('inventario.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small">Stock actual</div>
                <h2 class="mb-0">{{ $producto->stock }}</h2>
                <small>{{ $producto->unidad }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header"><i class="bi bi-list-ul me-2"></i>Movimientos</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Fecha</th>
                            <th>Tipo</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Antes</th>
                            <th class="text-center">Después</th>
                            <th>Motivo</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $m)
                        <tr>
                            <td class="ps-3"><small>{{ $m->created_at->format('d/m/Y H:i') }}</small></td>
                            <td><span class="badge bg-{{ $m->tipoBadge() }}">{{ $m->tipoTexto() }}</span></td>
                            <td class="text-center fw-bold {{ $m->cantidad > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $m->cantidad > 0 ? '+' : '' }}{{ $m->cantidad }}
                            </td>
                            <td class="text-center">{{ $m->stock_anterior }}</td>
                            <td class="text-center fw-bold">{{ $m->stock_nuevo }}</td>
                            <td><small>{{ $m->motivo }}</small>
                                @if($m->pedido)<br><a href="{{ route('pedidos.show', $m->pedido) }}" class="small">{{ $m->pedido->numero }}</a>@endif
                            </td>
                            <td><small>{{ $m->usuario->name ?? '—' }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Sin movimientos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $movimientos->links() }}</div>
        </div>
    </div>
</div>
@endsection
