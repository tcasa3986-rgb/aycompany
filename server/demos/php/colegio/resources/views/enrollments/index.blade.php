@extends('layouts.app')
@section('title', 'Matrículas')
@section('page-title', 'Gestión de Matrículas')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <p style="color:var(--muted);font-size:13px;">{{ $matriculas->total() }} matrículas encontradas</p>
    <a href="{{ route('matriculas.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Matrícula</a>
</div>

{{-- Filtros --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 22px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <input type="text" name="buscar" class="form-control"
                    placeholder="Nombre, apellido o DNI del alumno..." value="{{ request('buscar') }}">
            </div>
            <select name="grado_id" class="form-control" style="min-width:160px;">
                <option value="">Todos los grados</option>
                @foreach($grados as $g)
                    <option value="{{ $g->id }}" {{ request('grado_id')==$g->id ? 'selected':'' }}>{{ $g->nombre }}</option>
                @endforeach
            </select>
            <select name="anio" class="form-control" style="min-width:120px;">
                <option value="">Todos los años</option>
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('anio')==$y ? 'selected':'' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('matriculas.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N° Matrícula</th>
                    <th>Alumno</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Año</th>
                    <th>Fecha Matrícula</th>
                    <th>Estado</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($matriculas as $m)
                <tr>
                    <td><span style="font-family:monospace;font-size:12px;">{{ $m->numero }}</span></td>
                    <td>
                        <div style="font-weight:600;">{{ $m->alumno->nombre_completo ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $m->alumno->dni ?? '' }}</div>
                    </td>
                    <td>{{ $m->grado->nombre ?? '—' }}</td>
                    <td style="font-weight:700;color:var(--primary-l);">Sec. {{ $m->seccion->nombre ?? '—' }}</td>
                    <td>{{ $m->anio_escolar }}</td>
                    <td style="font-size:13px;">{{ $m->fecha_matricula?->format('d/m/Y') }}</td>
                    <td>
                        @php $ec = $m->estado === 'activo' ? 'badge-success' : ($m->estado === 'retirado' ? 'badge-danger' : 'badge-warning'); @endphp
                        <span class="badge {{ $ec }}">{{ ucfirst($m->estado) }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:center;">
                            <a href="{{ route('matriculas.show', $m) }}" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('matriculas.edit', $m) }}" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:var(--muted);">
                        <i class="fas fa-file-signature" style="font-size:36px;margin-bottom:12px;display:block;opacity:.3;"></i>
                        No se encontraron matrículas.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($matriculas->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--border);">
        {{ $matriculas->links() }}
    </div>
    @endif
</div>

@endsection
