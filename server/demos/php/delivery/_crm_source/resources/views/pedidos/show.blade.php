@extends('layouts.app')
@section('title', 'Pedido ' . $pedido->numero)

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
        <li class="breadcrumb-item active">{{ $pedido->numero }}</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title d-inline">{{ $pedido->numero }}</h4>
        <span class="badge bg-{{ $pedido->estado_badge }} ms-2 fs-6">{{ $pedido->estado_texto }}</span>
    </div>
    <div class="d-flex gap-2">
        <!-- Cambiar estado rápido -->
        @if(!in_array($pedido->estado, ['entregado','cancelado']))
        @can('editar pedidos')
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-arrow-repeat me-1"></i>Cambiar Estado
            </button>
            <ul class="dropdown-menu">
                @foreach(['confirmado'=>['info','Confirmar'],'preparando'=>['primary','En Preparación'],'listo'=>['secondary','Listo para enviar'],'en_camino'=>['info','Enviar'],'entregado'=>['success','Marcar Entregado'],'cancelado'=>['danger','Cancelar Pedido']] as $est=>$conf)
                @if($est !== $pedido->estado)
                <li>
                    <button class="dropdown-item" onclick="cambiarEstado('{{ $est }}')">
                        <span class="badge bg-{{ $conf[0] }} me-2">●</span>{{ $conf[1] }}
                    </button>
                </li>
                @endif
                @endforeach
            </ul>
        </div>
        @endcan
        @endif
        <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
    </div>
</div>

<div class="row g-3">
    <!-- Info del pedido -->
    <div class="col-lg-8">
        <!-- Items -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-list-check me-2 text-primary"></i>Productos del Pedido</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr>
                        <th class="ps-3">Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Precio Unit.</th>
                        <th class="text-end pe-3">Subtotal</th>
                    </tr></thead>
                    <tbody>
                    @foreach($pedido->items as $item)
                    <tr>
                        <td class="ps-3">{{ $item->nombre_producto }}
                            @if($item->notas)<div class="text-muted small">{{ $item->notas }}</div>@endif
                        </td>
                        <td class="text-center"><span class="badge bg-light text-dark border">x{{ $item->cantidad }}</span></td>
                        <td class="text-end">S/ {{ number_format($item->precio_unitario, 2) }}</td>
                        <td class="text-end pe-3 fw-semibold">S/ {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr><td colspan="3" class="text-end ps-3">Subtotal:</td><td class="text-end pe-3">S/ {{ number_format($pedido->subtotal, 2) }}</td></tr>
                        <tr><td colspan="3" class="text-end">Delivery:</td><td class="text-end pe-3">S/ {{ number_format($pedido->costo_delivery, 2) }}</td></tr>
                        @if($pedido->descuento > 0)
                        <tr class="text-danger"><td colspan="3" class="text-end">Descuento:</td><td class="text-end pe-3">- S/ {{ number_format($pedido->descuento, 2) }}</td></tr>
                        @endif
                        <tr class="table-primary"><td colspan="3" class="text-end fs-5">TOTAL:</td><td class="text-end pe-3 fs-5">S/ {{ number_format($pedido->total, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Repartidor / Asignar entrega -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bicycle me-2 text-primary"></i>Asignación de Entrega</span>
                @if(!in_array($pedido->estado, ['entregado','cancelado']))
                @can('asignar entregas')
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#formAsignar">
                    <i class="bi bi-person-plus me-1"></i>Asignar Repartidor
                </button>
                @endcan
                @endif
            </div>
            <div class="collapse {{ !$pedido->repartidor ? 'show' : '' }}" id="formAsignar">
                <div class="card-body border-top">
                    <form method="POST" action="{{ route('entregas.asignar') }}">
                        @csrf
                        <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <select name="repartidor_id" class="form-select" required>
                                    <option value="">Seleccionar repartidor...</option>
                                    @foreach($repartidores as $rep)
                                    <option value="{{ $rep->id }}" {{ $pedido->repartidor_id === $rep->id ? 'selected' : '' }}>
                                        {{ $rep->nombre_completo }} — {{ $rep->zona_asignada }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="datetime-local" name="fecha_entrega_estimada" class="form-control"
                                    value="{{ now()->addMinutes(45)->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-geo-alt me-1"></i>Asignar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if($pedido->repartidor)
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $pedido->repartidor->foto_url }}" class="avatar-md" alt="">
                    <div>
                        <div class="fw-bold">{{ $pedido->repartidor->nombre_completo }}</div>
                        <div class="text-muted small"><i class="{{ $pedido->repartidor->vehiculo_icono }} me-1"></i>{{ ucfirst($pedido->repartidor->tipo_vehiculo) }}</div>
                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $pedido->repartidor->telefono }}</div>
                    </div>
                    <div class="ms-auto text-end">
                        <span class="badge bg-{{ $pedido->repartidor->estado === 'disponible' ? 'success' : 'warning' }}">{{ ucfirst($pedido->repartidor->estado) }}</span>
                        <div class="text-muted small mt-1">⭐ {{ $pedido->repartidor->calificacion }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Historial de entregas -->
        @if($pedido->entregas->isNotEmpty())
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-geo me-2 text-primary"></i>Historial de Entrega</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th class="ps-3">Estado</th><th>Repartidor</th><th>Asignado</th><th>Entregado</th><th>Tiempo</th><th class="pe-3">Acción</th></tr></thead>
                    <tbody>
                    @foreach($pedido->entregas as $entrega)
                    <tr>
                        <td class="ps-3"><span class="badge bg-{{ $entrega->estado_badge }}">{{ $entrega->estado }}</span></td>
                        <td>{{ $entrega->repartidor?->nombre }}</td>
                        <td class="small text-muted">{{ $entrega->fecha_asignacion->format('H:i') }}</td>
                        <td class="small text-muted">{{ $entrega->fecha_entrega_real?->format('H:i') ?? '—' }}</td>
                        <td class="small">{{ $entrega->tiempo_minutos ? $entrega->tiempo_minutos . ' min' : '—' }}</td>
                        <td class="pe-3">
                            @if(!in_array($entrega->estado, ['entregado','fallido','devuelto']))
                            @can('actualizar entregas')
                            <form method="POST" action="{{ route('entregas.actualizar-estado', $entrega) }}" class="d-inline">
                                @csrf
                                <select name="estado" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                    @foreach(['recogido','en_camino','entregado','fallido','devuelto'] as $e)
                                    <option value="{{ $e }}" {{ $entrega->estado === $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @endcan
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Columna derecha -->
    <div class="col-lg-4">
        <!-- Info cliente -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person me-2"></i>Cliente</div>
            <div class="card-body">
                @if($pedido->cliente)
                <div class="fw-bold">{{ $pedido->cliente->nombre_completo }}</div>
                <div class="text-muted small mb-1"><i class="bi bi-telephone me-1"></i>{{ $pedido->cliente->telefono }}</div>
                <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $pedido->direccion_entrega }}</div>
                @if($pedido->distrito_entrega)<div class="text-muted small">{{ $pedido->distrito_entrega }}</div>@endif
                @if($pedido->referencia_entrega)<div class="text-info small mt-1"><i class="bi bi-info-circle me-1"></i>{{ $pedido->referencia_entrega }}</div>@endif
                <a href="{{ route('clientes.show', $pedido->cliente) }}" class="btn btn-sm btn-outline-info mt-2">Ver cliente</a>
                @endif
            </div>
        </div>

        <!-- Pago -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-cash me-2"></i>Pago</span>
                <span class="badge bg-{{ $pedido->estado_pago === 'pagado' ? 'success' : ($pedido->estado_pago === 'parcial' ? 'warning' : 'danger') }}">
                    {{ ucfirst($pedido->estado_pago) }}
                </span>
            </div>
            <div class="card-body">
                <div class="mb-2 small"><strong>Método:</strong> {{ ucfirst($pedido->tipo_pago) }}</div>
                <div class="mb-2 small"><strong>Total:</strong> S/ {{ number_format($pedido->total, 2) }}</div>
                @if($pedido->pagos->isNotEmpty())
                @foreach($pedido->pagos as $pago)
                <div class="small text-success"><i class="bi bi-check-circle me-1"></i>Pagado S/ {{ number_format($pago->monto, 2) }} — {{ $pago->fecha_pago->format('d/m H:i') }}</div>
                @endforeach
                @endif
                @if($pedido->estado_pago !== 'pagado')
                @can('registrar pagos')
                <button class="btn btn-sm btn-success mt-2 w-100" data-bs-toggle="collapse" data-bs-target="#formPago">
                    <i class="bi bi-plus me-1"></i>Registrar Pago
                </button>
                <div class="collapse mt-2" id="formPago">
                    <form method="POST" action="{{ route('pagos.store') }}">
                        @csrf
                        <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
                        <select name="metodo" class="form-select form-select-sm mb-2">
                            @foreach(['efectivo','tarjeta','transferencia','yape','plin'] as $m)
                            <option value="{{ $m }}" {{ $pedido->tipo_pago === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                            @endforeach
                        </select>
                        <input type="number" step="0.01" name="monto" value="{{ $pedido->total }}" class="form-control form-control-sm mb-2" placeholder="Monto" required>
                        <button type="submit" class="btn btn-success btn-sm w-100">Confirmar Pago</button>
                    </form>
                </div>
                @endcan
                @endif
            </div>
        </div>

        <!-- Info adicional -->
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Información</div>
            <div class="card-body small">
                <div class="mb-1"><strong>Creado:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                <div class="mb-1"><strong>Operador:</strong> {{ $pedido->operador?->name }}</div>
                @if($pedido->fecha_programada)<div class="mb-1"><strong>Programado:</strong> {{ $pedido->fecha_programada->format('d/m H:i') }}</div>@endif
                @if($pedido->fecha_entrega)<div class="mb-1"><strong>Entregado:</strong> {{ $pedido->fecha_entrega->format('d/m H:i') }}</div>@endif
                @if($pedido->notas)<div class="mb-1"><strong>Notas:</strong> {{ $pedido->notas }}</div>@endif
                @if($pedido->motivo_cancelacion)<div class="text-danger"><strong>Cancelación:</strong> {{ $pedido->motivo_cancelacion }}</div>@endif
            </div>
        </div>
    </div>
</div>

<!-- Modal cambiar estado -->
<form id="formEstado" method="POST" action="{{ route('pedidos.cambiar-estado', $pedido) }}">
    @csrf
    <input type="hidden" name="estado" id="nuevoEstado">
</form>
@endsection

@push('scripts')
<script>
function cambiarEstado(estado) {
    if(confirm('¿Cambiar el estado del pedido a: ' + estado.replace(/_/g,' ').toUpperCase() + '?')) {
        document.getElementById('nuevoEstado').value = estado;
        document.getElementById('formEstado').submit();
    }
}
</script>
@endpush
