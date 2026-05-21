@extends('layouts.app')
@section('title', 'Cliente: ' . $cliente->nombre_completo)

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
        <li class="breadcrumb-item active">{{ $cliente->nombre_completo }}</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-person me-2 text-primary"></i>{{ $cliente->nombre_completo }}</h4>
    <div>
        @can('editar clientes')
        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Editar</a>
        @endcan
        @can('crear pedidos')
        <a href="{{ route('pedidos.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-primary ms-2">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Pedido
        </a>
        @endcan
    </div>
</div>

<div class="row g-3">
    <!-- Info del cliente -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($cliente->nombre_completo) }}&background=0D6EFD&color=fff&size=128"
                     class="rounded-circle mb-3" width="80" height="80" alt="">
                <h5 class="fw-bold mb-1">{{ $cliente->nombre_completo }}</h5>
                @php $tipoBadge = ['regular'=>'secondary','frecuente'=>'info','vip'=>'warning'][$cliente->tipo] ?? 'secondary'; @endphp
                <span class="badge bg-{{ $tipoBadge }} mb-3">{{ ucfirst($cliente->tipo) }}</span>
                <div class="text-start">
                    <div class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i>{{ $cliente->telefono }}</div>
                    @if($cliente->telefono_alt)<div class="mb-2"><i class="bi bi-telephone-plus me-2 text-muted"></i>{{ $cliente->telefono_alt }}</div>@endif
                    @if($cliente->email)<div class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i>{{ $cliente->email }}</div>@endif
                    <div class="mb-2"><i class="bi bi-geo-alt me-2 text-muted"></i>{{ $cliente->direccion }}</div>
                    @if($cliente->distrito)<div class="mb-2"><i class="bi bi-map me-2 text-muted"></i>{{ $cliente->distrito }}, {{ $cliente->ciudad }}</div>@endif
                    @if($cliente->referencia)<div class="mb-2 text-muted small"><i class="bi bi-info-circle me-2"></i>{{ $cliente->referencia }}</div>@endif
                </div>
            </div>
        </div>
        <!-- Estadísticas -->
        <div class="card mt-3">
            <div class="card-header">Estadísticas</div>
            <div class="card-body">
                <div class="row text-center g-2">
                    <div class="col-6">
                        <div class="fw-bold fs-4 text-primary">{{ $cliente->total_pedidos }}</div>
                        <small class="text-muted">Total Pedidos</small>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold fs-4 text-success">S/ {{ number_format($cliente->total_gastado, 2) }}</div>
                        <small class="text-muted">Total Gastado</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de pedidos -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2 text-primary"></i>Historial de Pedidos
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">N° Pedido</th>
                                <th>Fecha</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Estado Pago</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($pedidos as $pedido)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('pedidos.show', $pedido) }}" class="fw-semibold text-decoration-none">{{ $pedido->numero }}</a>
                                </td>
                                <td class="small text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                <td class="small">{{ $pedido->items->count() }} producto(s)</td>
                                <td><strong>S/ {{ number_format($pedido->total, 2) }}</strong></td>
                                <td><span class="badge bg-{{ $pedido->estado_pago === 'pagado' ? 'success' : ($pedido->estado_pago === 'parcial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($pedido->estado_pago) }}
                                </span></td>
                                <td><span class="badge bg-{{ $pedido->estado_badge }}">{{ $pedido->estado_texto }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Sin pedidos registrados</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if($cliente->notas)
        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-sticky me-2"></i>Notas Internas</div>
            <div class="card-body text-muted">{{ $cliente->notas }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
