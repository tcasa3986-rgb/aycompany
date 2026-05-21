@extends('layouts.app')
@section('title', 'Reporte de Alumnos')
@section('page-title', 'Reporte de Alumnos Matriculados')

@push('styles')
<style>@media print { .sidebar,.topbar,.no-print{display:none!important} .main-content{margin-left:0!important} .page-body{padding:0!important} }</style>
@endpush

@section('content')

<div class="no-print" style="display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('reportes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Reportes</a>
    <button onclick="window.print()" class="btn btn-success" style="margin-left:auto;"><i class="fas fa-print"></i> Imprimir</button>
    <a href="{{ route('reportes.exportar', 'alumnos') }}?anio={{ $anio }}" class="btn btn-primary"><i class="fas fa-download"></i> Exportar CSV</a>
</div>

{{-- Filtros --}}
<div class="card no-print" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 22px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div>
                <label class="form-label" style="margin-bottom:4px;">Año Escolar</label>
                <select name="anio" class="form-control" style="min-width:100px;">
                    @for($y=date('Y');$y>=2020;$y--)
                    <option value="{{ $y }}" {{ $anio==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label" style="margin-bottom:4px;">Grado</label>
                <select name="grado_id" class="form-control" style="min-width:160px;">
                    <option value="">Todos los grados</option>
                    @foreach($grados as $g)
                    <option value="{{ $g->id }}" {{ $gradoId==$g->id?'selected':'' }}>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Alumnos Matriculados — {{ $anio }}</span>
        <span class="badge badge-primary">{{ $matriculas->total() }} alumnos</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Código</th><th>Apellidos y Nombres</th><th>DNI</th><th>Grado</th><th>Sección</th><th>Apoderado</th><th>Teléfono</th></tr>
            </thead>
            <tbody>
            @forelse($matriculas as $i => $m)
                <tr>
                    <td style="color:var(--muted);font-size:13px;">{{ $matriculas->firstItem()+$i }}</td>
                    <td style="font-family:monospace;font-size:12px;">{{ $m->alumno->codigo ?? '—' }}</td>
                    <td style="font-weight:600;">{{ $m->alumno->apellidos ?? '' }}, {{ $m->alumno->nombres ?? '—' }}</td>
                    <td style="font-family:monospace;font-size:12px;">{{ $m->alumno->dni ?? '—' }}</td>
                    <td>{{ $m->grado->nombre ?? '—' }}</td>
                    <td style="font-weight:700;color:var(--primary-l);">{{ $m->seccion->nombre ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $m->alumno->apoderado_nombre ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $m->alumno->apoderado_telefono ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;padding:48px;color:var(--muted);">Sin alumnos para los filtros seleccionados</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($matriculas->hasPages())
    <div class="no-print" style="padding:16px 22px;border-top:1px solid var(--border);">
        {{ $matriculas->links() }}
    </div>
    @endif
</div>
@endsection
