@extends('layouts.app')
@section('title', 'Reporte de Pagos')
@section('page-title', 'Reporte de Pagos')

@push('styles')
<style>@media print { .sidebar,.topbar,.no-print{display:none!important} .main-content{margin-left:0!important} .page-body{padding:0!important} }</style>
@endpush

@section('content')

<div class="no-print" style="display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('reportes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Reportes</a>
    <button onclick="window.print()" class="btn btn-success" style="margin-left:auto;">
        <i class="fas fa-print"></i> Imprimir
    </button>
    <a href="{{ route('reportes.exportar', 'pagos') }}?anio={{ $anio }}&mes={{ $mes }}" class="btn btn-primary">
        <i class="fas fa-download"></i> Exportar CSV
    </a>
</div>

{{-- Filtros --}}
<div class="card no-print" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 22px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div>
                <label class="form-label" style="margin-bottom:4px;">Año</label>
                <select name="anio" class="form-control" style="min-width:100px;">
                    @for($y=date('Y');$y>=2020;$y--)
                    <option value="{{ $y }}" {{ $anio==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label" style="margin-bottom:4px;">Mes</label>
                <select name="mes" class="form-control" style="min-width:130px;">
                    <option value="">Todos los meses</option>
                    @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i=>$m)
                    <option value="{{ $i+1 }}" {{ $mes==$i+1?'selected':'' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
        </form>
    </div>
</div>

{{-- Encabezado de impresión --}}
<div style="text-align:center;margin-bottom:20px;display:none;" class="print-header">
    <h2 style="font-size:20px;font-weight:800;">REPORTE DE PAGOS — {{ $anio }}</h2>
    <p style="color:#64748b;font-size:13px;">Generado el {{ now()->format('d/m/Y H:i') }}</p>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Pagos Registrados</span>
        <span style="font-size:13px;font-weight:700;color:var(--success);">
            Total: S/. {{ number_format($pagos->sum('monto_pagado'), 2) }}
        </span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Recibo</th><th>Alumno</th><th>DNI</th><th>Concepto</th>
                    <th>Mes / Año</th><th>Monto</th><th>Método</th><th>Fecha</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pagos as $p)
                <tr>
                    <td style="font-family:monospace;font-size:12px;">{{ $p->numero_recibo }}</td>
                    <td style="font-weight:600;">{{ $p->alumno->nombre_completo ?? '—' }}</td>
                    <td style="font-family:monospace;font-size:12px;">{{ $p->alumno->dni ?? '—' }}</td>
                    <td>{{ $p->concepto->nombre ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $p->nombre_mes }} {{ $p->anio_escolar }}</td>
                    <td style="font-weight:700;color:var(--primary);">S/. {{ number_format($p->monto_pagado,2) }}</td>
                    <td style="text-transform:capitalize;font-size:12px;">{{ $p->metodo_pago }}</td>
                    <td style="font-size:12px;">{{ $p->fecha_pago?->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;padding:48px;color:var(--muted);">Sin pagos para los filtros seleccionados</td></tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr style="background:#f8fafc;font-weight:700;">
                    <td colspan="5" style="padding:12px 16px;text-align:right;font-size:13px;">TOTAL GENERAL:</td>
                    <td style="padding:12px 16px;font-size:15px;color:var(--success);">S/. {{ number_format($pagos->sum('monto_pagado'),2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @if($pagos->hasPages())
    <div class="no-print" style="padding:16px 22px;border-top:1px solid var(--border);">
        {{ $pagos->links() }}
    </div>
    @endif
</div>
@endsection
