@extends('layouts.app')
@section('title', 'Reporte de Clientes')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li class="breadcrumb-item active">Clientes</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-people me-2" style="color:#6f42c1"></i>Clientes más Activos</h4>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label small fw-semibold">Desde</label>
                <input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-semibold">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}" class="form-control"></div>
            <div class="col-auto d-flex align-items-end">
                <button type="submit" class="btn" style="background:#6f42c1;color:#fff"><i class="bi bi-search me-1"></i>Generar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Período: {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Tipo</th>
                        <th class="text-center">Pedidos en período</th>
                        <th class="text-end pe-3">Total Gastado</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($clientes as $i => $c)
                <tr>
                    <td class="ps-3">
                        @if($i < 3)
                        <span class="badge bg-{{ ['warning','secondary','dark'][$i] }}">{{ $i+1 }}</span>
                        @else
                        <span class="text-muted">{{ $i+1 }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('clientes.show', $c) }}" class="fw-semibold text-decoration-none">{{ $c->nombre_completo }}</a>
                        @if($c->email)<div class="text-muted small">{{ $c->email }}</div>@endif
                    </td>
                    <td class="small">{{ $c->telefono }}</td>
                    <td>
                        @php $tipoBadge = ['regular'=>'secondary','frecuente'=>'info','vip'=>'warning'][$c->tipo] ?? 'secondary'; @endphp
                        <span class="badge bg-{{ $tipoBadge }}">{{ ucfirst($c->tipo) }}</span>
                    </td>
                    <td class="text-center"><span class="badge bg-primary">{{ $c->pedidos_periodo }}</span></td>
                    <td class="text-end pe-3 fw-bold text-success">S/ {{ number_format($c->gasto_periodo ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Sin datos en este período</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
