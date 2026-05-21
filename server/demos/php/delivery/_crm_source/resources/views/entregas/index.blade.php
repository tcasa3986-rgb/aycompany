@extends('layouts.app')
@section('title', 'Entregas')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Entregas</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-geo-alt me-2 text-primary"></i>Gestión de Entregas</h4>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    @foreach(['asignado','recogido','en_camino','entregado','fallido','devuelto'] as $e)
                    <option value="{{ $e }}" {{ request('estado')===$e?'selected':'' }}>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="repartidor_id" class="form-select">
                    <option value="">Todos los repartidores</option>
                    @foreach($repartidores as $rep)
                    <option value="{{ $rep->id }}" {{ request('repartidor_id')==$rep->id?'selected':'' }}>{{ $rep->nombre_completo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha" value="{{ request('fecha', date('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="{{ route('entregas.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Pedido</th>
                        <th>Cliente</th>
                        <th>Dirección</th>
                        <th>Repartidor</th>
                        <th>Asignado</th>
                        <th>Est. Entrega</th>
                        <th>Estado</th>
                        <th class="pe-3">Actualizar</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($entregas as $entrega)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('pedidos.show', $entrega->pedido) }}" class="fw-semibold text-decoration-none">{{ $entrega->pedido?->numero }}</a>
                        </td>
                        <td class="small">{{ $entrega->pedido?->cliente?->nombre_completo }}</td>
                        <td class="small">
                            {{ Str::limit($entrega->pedido?->direccion_entrega, 35) }}
                            @if($entrega->pedido?->distrito_entrega)<div class="text-muted">{{ $entrega->pedido->distrito_entrega }}</div>@endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $entrega->repartidor?->foto_url }}" class="avatar-sm" alt="">
                                <span class="small">{{ $entrega->repartidor?->nombre }}</span>
                            </div>
                        </td>
                        <td class="small text-muted">{{ $entrega->fecha_asignacion->format('H:i') }}</td>
                        <td class="small text-muted">{{ $entrega->fecha_entrega_estimada?->format('H:i') ?? '—' }}</td>
                        <td><span class="badge bg-{{ $entrega->estado_badge }}">{{ ucfirst($entrega->estado) }}</span></td>
                        <td class="pe-3">
                            @if(!in_array($entrega->estado, ['entregado','fallido','devuelto']))
                            @can('actualizar entregas')
                            <form method="POST" action="{{ route('entregas.actualizar-estado', $entrega) }}">
                                @csrf
                                <div class="d-flex gap-1">
                                    <select name="estado" class="form-select form-select-sm" style="width:120px">
                                        @foreach(['recogido','en_camino','entregado','fallido'] as $e)
                                        <option value="{{ $e }}" {{ $entrega->estado === $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check"></i></button>
                                </div>
                            </form>
                            @endcan
                            @else
                            <span class="text-muted small">Finalizado</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-geo display-4 d-block mb-2"></i>No hay entregas para mostrar
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($entregas->hasPages())
    <div class="card-footer d-flex justify-content-between">
        <small class="text-muted">{{ $entregas->total() }} entregas</small>
        {{ $entregas->links() }}
    </div>
    @endif
</div>
@endsection
