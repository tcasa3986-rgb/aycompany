@extends('layouts.app')
@section('title', 'Libro de Notas')
@section('page-title', 'Libro de Notas')

@section('content')

{{-- Filtros --}}
<div class="card" style="margin-bottom:24px;">
    <form method="GET" action="{{ route('notas.index') }}" style="display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap;">
        <div class="form-group" style="margin:0;flex:1;min-width:160px;">
            <label class="form-label">Año Escolar</label>
            <select name="anio" class="form-control">
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group" style="margin:0;flex:2;min-width:180px;">
            <label class="form-label">Sección *</label>
            <select name="seccion_id" class="form-control" required>
                <option value="">— Seleccionar —</option>
                @foreach($secciones as $s)
                    <option value="{{ $s->id }}" {{ $seccionId == $s->id ? 'selected' : '' }}>
                        {{ $s->grado->nombre }} — Sec. {{ $s->nombre }} ({{ ucfirst($s->turno) }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;flex:2;min-width:180px;">
            <label class="form-label">Materia *</label>
            <select name="materia_id" class="form-control" required>
                <option value="">— Seleccionar —</option>
                @foreach($materias as $m)
                    <option value="{{ $m->id }}" {{ $materiaId == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex-shrink:0;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Ver Notas</button>
        </div>
    </form>
</div>

@if($seccion && $materia)

{{-- Encabezado --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
    <div>
        <h3 style="font-size:16px;font-weight:700;">
            {{ $materia->nombre }} — {{ $seccion->grado->nombre }}, Sección {{ $seccion->nombre }}
        </h3>
        <p style="font-size:12px;color:var(--muted);margin-top:2px;">Año escolar {{ $anio }} · {{ $libroNotas->count() }} alumno(s)</p>
    </div>
    <a href="{{ route('notas.index') }}?seccion_id={{ $seccionId }}&materia_id={{ $materiaId }}&anio={{ $anio }}&imprimir=1"
       class="btn btn-secondary btn-sm" onclick="window.print();return false;">
        <i class="fas fa-print"></i> Imprimir
    </a>
</div>

{{-- Tabs por bimestre --}}
<div style="display:flex;gap:6px;margin-bottom:0;flex-wrap:wrap;" id="tabs-bimestre">
    @for($b = 1; $b <= 4; $b++)
    <button onclick="cambiarBimestre({{ $b }})" id="tab-{{ $b }}"
        class="btn btn-sm {{ $b == 1 ? 'btn-primary' : 'btn-secondary' }}"
        style="border-radius:10px 10px 0 0;">
        {{ $b }}° Bimestre
    </button>
    @endfor
    <button onclick="cambiarBimestre(0)" id="tab-0"
        class="btn btn-sm btn-secondary" style="border-radius:10px 10px 0 0;">
        <i class="fas fa-table"></i> Resumen
    </button>
</div>

{{-- Panel de ingreso por bimestre --}}
@for($b = 1; $b <= 4; $b++)
<div id="panel-{{ $b }}" class="panel-bimestre" style="{{ $b > 1 ? 'display:none;' : '' }}">
    <div class="card" style="border-radius:0 12px 12px 12px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h4 style="font-size:14px;font-weight:700;color:var(--primary);">
                <i class="fas fa-edit" style="margin-right:6px;"></i>Ingreso de Notas — {{ $b }}° Bimestre
            </h4>
        </div>
        <form method="POST" action="{{ route('notas.guardar') }}">
            @csrf
            <input type="hidden" name="seccion_id"   value="{{ $seccionId }}">
            <input type="hidden" name="materia_id"   value="{{ $materiaId }}">
            <input type="hidden" name="anio_escolar" value="{{ $anio }}">
            <input type="hidden" name="bimestre"     value="{{ $b }}">

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Alumno</th>
                            <th style="text-align:center;width:120px;">Nota (0–20)</th>
                            <th style="text-align:center;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($libroNotas as $i => $row)
                        @php $notaObj = $row['bimestres'][$b]; @endphp
                        <tr>
                            <td style="color:var(--muted);font-size:12px;">{{ $i + 1 }}</td>
                            <td>
                                <div style="font-weight:600;">{{ $row['alumno']->nombre_completo }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $row['alumno']->codigo }}</div>
                            </td>
                            <td>
                                <input type="number"
                                    name="notas[{{ $row['alumno']->id }}]"
                                    value="{{ $notaObj?->nota ?? '' }}"
                                    min="0" max="20" step="0.5"
                                    class="form-control nota-input"
                                    style="text-align:center;padding:8px;"
                                    placeholder="—"
                                    oninput="actualizarEstado(this)">
                            </td>
                            <td style="text-align:center;">
                                @if($notaObj)
                                    <span class="badge {{ $notaObj->nota >= 11 ? 'badge-success' : 'badge-danger' }}">
                                        {{ $notaObj->nota >= 11 ? 'Aprobado' : 'Desaprobado' }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:16px;gap:10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar {{ $b }}° Bimestre
                </button>
            </div>
        </form>
    </div>
</div>
@endfor

{{-- Panel resumen --}}
<div id="panel-0" style="display:none;">
    <div class="card" style="border-radius:0 12px 12px 12px;">
        <div style="margin-bottom:16px;">
            <h4 style="font-size:14px;font-weight:700;color:var(--primary);">
                <i class="fas fa-table" style="margin-right:6px;"></i>Resumen General de Notas
            </h4>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Alumno</th>
                        <th style="text-align:center;">B1</th>
                        <th style="text-align:center;">B2</th>
                        <th style="text-align:center;">B3</th>
                        <th style="text-align:center;">B4</th>
                        <th style="text-align:center;">Promedio</th>
                        <th style="text-align:center;">Estado</th>
                        <th style="text-align:center;">Boleta</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($libroNotas as $i => $row)
                    <tr>
                        <td style="color:var(--muted);font-size:12px;">{{ $i + 1 }}</td>
                        <td>
                            <div style="font-weight:600;">{{ $row['alumno']->nombre_completo }}</div>
                            <div style="font-size:11px;color:var(--muted);">{{ $row['alumno']->codigo }}</div>
                        </td>
                        @for($b = 1; $b <= 4; $b++)
                            @php $n = $row['bimestres'][$b]; @endphp
                            <td style="text-align:center;">
                                @if($n)
                                    <span style="font-weight:700;color:{{ $n->nota >= 11 ? 'var(--success)' : 'var(--danger)' }};">
                                        {{ number_format($n->nota, 1) }}
                                    </span>
                                @else
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                        @endfor
                        <td style="text-align:center;font-weight:800;font-size:15px;
                            color:{{ $row['promedio'] !== null ? ($row['promedio'] >= 11 ? 'var(--success)' : 'var(--danger)') : 'var(--muted)' }};">
                            {{ $row['promedio'] !== null ? number_format($row['promedio'], 2) : '—' }}
                        </td>
                        <td style="text-align:center;">
                            @if($row['estado'] === 'aprobado')
                                <span class="badge badge-success">Aprobado</span>
                            @elseif($row['estado'] === 'desaprobado')
                                <span class="badge badge-danger">Desaprobado</span>
                            @else
                                <span class="badge badge-secondary">Pendiente</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <a href="{{ route('notas.boleta', $row['alumno']->id) }}?anio={{ $anio }}"
                               class="btn btn-sm btn-secondary btn-icon" title="Ver boleta">
                                <i class="fas fa-file-alt"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Estadísticas rápidas --}}
        @php
            $aprobados   = $libroNotas->where('estado','aprobado')->count();
            $desaprobados= $libroNotas->where('estado','desaprobado')->count();
            $pendientes  = $libroNotas->where('estado','pendiente')->count();
            $total       = $libroNotas->count();
        @endphp
        @if($total > 0)
        <div style="display:flex;gap:16px;margin-top:20px;flex-wrap:wrap;">
            <div style="flex:1;min-width:120px;text-align:center;padding:14px;background:#d1fae5;border-radius:12px;">
                <div style="font-size:24px;font-weight:800;color:#065f46;">{{ $aprobados }}</div>
                <div style="font-size:12px;color:#065f46;">Aprobados</div>
            </div>
            <div style="flex:1;min-width:120px;text-align:center;padding:14px;background:#fee2e2;border-radius:12px;">
                <div style="font-size:24px;font-weight:800;color:#7f1d1d;">{{ $desaprobados }}</div>
                <div style="font-size:12px;color:#7f1d1d;">Desaprobados</div>
            </div>
            <div style="flex:1;min-width:120px;text-align:center;padding:14px;background:#f1f5f9;border-radius:12px;">
                <div style="font-size:24px;font-weight:800;color:var(--muted);">{{ $pendientes }}</div>
                <div style="font-size:12px;color:var(--muted);">Pendientes</div>
            </div>
            <div style="flex:1;min-width:120px;text-align:center;padding:14px;background:#e0e7ff;border-radius:12px;">
                <div style="font-size:24px;font-weight:800;color:#3730a3;">
                    {{ $total > 0 ? round(($aprobados/$total)*100) : 0 }}%
                </div>
                <div style="font-size:12px;color:#3730a3;">Aprobación</div>
            </div>
        </div>
        @endif
    </div>
</div>

@else
<div class="card" style="padding:48px;text-align:center;color:var(--muted);">
    <i class="fas fa-clipboard-list" style="font-size:48px;opacity:.2;display:block;margin-bottom:16px;"></i>
    <p style="font-size:15px;font-weight:600;">Selecciona una sección y materia para ver el libro de notas.</p>
</div>
@endif

@endsection

@push('scripts')
<script>
function cambiarBimestre(b) {
    // Ocultar todos los panels
    document.querySelectorAll('.panel-bimestre').forEach(p => p.style.display = 'none');
    document.getElementById('panel-0').style.display = 'none';

    // Quitar active de tabs
    for(let i = 0; i <= 4; i++) {
        const t = document.getElementById('tab-' + i);
        if(t) { t.classList.remove('btn-primary'); t.classList.add('btn-secondary'); }
    }

    // Mostrar panel activo
    const panel = document.getElementById('panel-' + b);
    if(panel) panel.style.display = 'block';
    const tab = document.getElementById('tab-' + b);
    if(tab) { tab.classList.remove('btn-secondary'); tab.classList.add('btn-primary'); }
}

function actualizarEstado(input) {
    const val = parseFloat(input.value);
    const row = input.closest('tr');
    const estadoCell = row.querySelector('td:last-child span') || row.cells[3].querySelector('span');
    if(!estadoCell) return;
    if(isNaN(val) || input.value === '') {
        estadoCell.className = 'badge badge-secondary';
        estadoCell.textContent = 'Pendiente';
    } else if(val >= 11) {
        estadoCell.className = 'badge badge-success';
        estadoCell.textContent = 'Aprobado';
    } else {
        estadoCell.className = 'badge badge-danger';
        estadoCell.textContent = 'Desaprobado';
    }
}
</script>
@endpush
