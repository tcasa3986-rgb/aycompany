@extends('layouts.app')
@section('title', 'Pagos')
@section('page-title', 'Gestión de Pagos')

@section('content')

{{-- Resumen financiero --}}
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <div class="stat-value">S/. {{ number_format($resumen['total_pagado'], 2) }}</div>
            <div class="stat-label">Cobrado este mes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value">S/. {{ number_format($resumen['total_pendiente'], 2) }}</div>
            <div class="stat-label">Pendiente de cobro</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-info">
            <div class="stat-value">S/. {{ number_format($resumen['total_vencido'], 2) }}</div>
            <div class="stat-label">Deuda vencida</div>
        </div>
    </div>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <p style="color:var(--muted);font-size:13px;">{{ $pagos->total() }} registros encontrados</p>
    <a href="{{ route('pagos.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Registrar Pago
    </a>
</div>

{{-- Filtros --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 22px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <input type="text" name="buscar" class="form-control"
                    placeholder="Alumno, DNI o N° recibo..." value="{{ request('buscar') }}">
            </div>
            <select name="estado" class="form-control" style="min-width:140px;">
                <option value="">Todos los estados</option>
                <option value="pagado"   {{ request('estado')=='pagado'   ? 'selected':'' }}>Pagado</option>
                <option value="pendiente"{{ request('estado')=='pendiente'? 'selected':'' }}>Pendiente</option>
                <option value="vencido"  {{ request('estado')=='vencido'  ? 'selected':'' }}>Vencido</option>
                <option value="anulado"  {{ request('estado')=='anulado'  ? 'selected':'' }}>Anulado</option>
            </select>
            <select name="mes" class="form-control" style="min-width:130px;">
                <option value="">Todos los meses</option>
                @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $mes)
                    <option value="{{ $i+1 }}" {{ request('mes')==($i+1) ? 'selected':'' }}>{{ $mes }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('pagos.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N° Recibo</th>
                    <th>Alumno</th>
                    <th>Concepto</th>
                    <th>Mes / Año</th>
                    <th>Monto</th>
                    <th>Fecha Pago</th>
                    <th>Método</th>
                    <th>Estado</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pagos as $pago)
                <tr>
                    <td><span style="font-family:monospace;font-size:12px;">{{ $pago->numero_recibo }}</span></td>
                    <td style="font-weight:600;">{{ $pago->alumno->nombre_completo ?? '—' }}</td>
                    <td>{{ $pago->concepto->nombre ?? '—' }}</td>
                    <td style="font-size:12px;color:var(--muted);">
                        {{ $pago->nombre_mes }} {{ $pago->anio_escolar }}
                    </td>
                    <td style="font-weight:700;color:var(--primary);">S/. {{ number_format($pago->monto_pagado, 2) }}</td>
                    <td style="font-size:13px;">{{ $pago->fecha_pago?->format('d/m/Y') }}</td>
                    <td>
                        <span style="font-size:12px;text-transform:capitalize;">{{ $pago->metodo_pago }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $pago->estado_badge }}">{{ ucfirst($pago->estado) }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:center;">
                            <a href="{{ route('pagos.show', $pago) }}" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('pagos.edit', $pago) }}" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px;color:var(--muted);">
                        <i class="fas fa-receipt" style="font-size:36px;margin-bottom:12px;display:block;opacity:.3;"></i>
                        No se encontraron pagos.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($pagos->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:13px;color:var(--muted);">Mostrando {{ $pagos->firstItem() }}–{{ $pagos->lastItem() }} de {{ $pagos->total() }}</span>
        {{ $pagos->links() }}
    </div>
    @endif
</div>

@endsection
