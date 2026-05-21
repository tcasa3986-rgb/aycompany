@extends('layouts.app')
@section('title', 'Reporte de Deudas')
@section('page-title', 'Reporte de Deudas Pendientes')

@push('styles')
<style>@media print { .sidebar,.topbar,.no-print{display:none!important} .main-content{margin-left:0!important} .page-body{padding:0!important} }</style>
@endpush

@section('content')

<div class="no-print" style="display:flex;gap:12px;align-items:center;margin-bottom:20px;">
    <a href="{{ route('reportes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Reportes</a>
    <button onclick="window.print()" class="btn btn-success" style="margin-left:auto;"><i class="fas fa-print"></i> Imprimir</button>
    <a href="{{ route('reportes.exportar', 'deudas') }}" class="btn btn-primary"><i class="fas fa-download"></i> Exportar CSV</a>
</div>

{{-- Resumen --}}
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $alumnos->count() }}</div>
            <div class="stat-label">Alumnos con deuda</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-hand-holding-usd"></i></div>
        <div class="stat-info">
            <div class="stat-value">S/. {{ number_format($totalDeuda, 2) }}</div>
            <div class="stat-label">Deuda total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-calculator"></i></div>
        <div class="stat-info">
            <div class="stat-value">S/. {{ $alumnos->count() > 0 ? number_format($totalDeuda / $alumnos->count(), 2) : '0.00' }}</div>
            <div class="stat-label">Deuda promedio</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-exclamation-triangle" style="color:var(--danger);margin-right:8px;"></i>Alumnos con Pagos Pendientes</span>
        <span style="font-size:12px;color:var(--muted);">Ordenado por mayor deuda</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Alumno</th><th>Grado</th><th>Conceptos pendientes</th><th style="text-align:right;">Deuda Total</th><th class="no-print">Acciones</th></tr>
            </thead>
            <tbody>
            @forelse($alumnos as $i => $alumno)
                @php $mat = $alumno->matriculaActiva(); @endphp
                <tr>
                    <td style="font-size:13px;color:var(--muted);">{{ $i+1 }}</td>
                    <td>
                        <div style="font-weight:700;">{{ $alumno->nombre_completo }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $alumno->codigo }} · DNI: {{ $alumno->dni }}</div>
                        @if($alumno->apoderado_nombre)
                        <div style="font-size:11px;color:var(--muted);">Apoderado: {{ $alumno->apoderado_nombre }} · {{ $alumno->apoderado_telefono }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $mat?->grado?->nombre ?? '—' }}</td>
                    <td>
                        @foreach($alumno->pagos as $p)
                        <div style="font-size:12px;padding:2px 0;">
                            <span class="badge badge-warning">{{ $p->concepto->nombre ?? '—' }}</span>
                            <span style="margin-left:4px;color:var(--muted);">S/. {{ number_format($p->monto,2) }}</span>
                        </div>
                        @endforeach
                    </td>
                    <td style="text-align:right;font-size:17px;font-weight:800;color:var(--danger);">
                        S/. {{ number_format($alumno->deuda_total, 2) }}
                    </td>
                    <td class="no-print">
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('pagos.create') }}?alumno_id={{ $alumno->id }}" class="btn btn-sm btn-primary btn-icon" title="Registrar pago"><i class="fas fa-money-bill"></i></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:48px;color:var(--muted);">
                    <i class="fas fa-check-circle" style="font-size:40px;color:var(--success);display:block;margin-bottom:12px;"></i>
                    ¡Sin deudas pendientes! Todos los alumnos están al día.
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
