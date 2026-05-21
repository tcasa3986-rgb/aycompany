@extends('layouts.app')
@section('title', 'Editar Matrícula')
@section('page-title', 'Editar Matrícula')

@section('content')
<div style="max-width:760px;">
<div style="margin-bottom:16px;">
    <a href="{{ route('matriculas.show', $matricula) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<form method="POST" action="{{ route('matriculas.update', $matricula) }}">
@csrf @method('PUT')
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-edit" style="color:#3b82f6;margin-right:8px;"></i>Editar Matrícula — {{ $matricula->numero }}</span>
    </div>
    <div class="card-body">
        {{-- Alumno (solo lectura) --}}
        <div class="form-group">
            <label class="form-label">Alumno</label>
            <div style="padding:10px 14px;background:#f8fafc;border:1.5px solid var(--border);border-radius:10px;font-weight:600;color:var(--text);">
                {{ $matricula->alumno->nombre_completo ?? '—' }} — DNI: {{ $matricula->alumno->dni ?? '—' }}
            </div>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Grado *</label>
                <select name="grado_id" id="grado_select" class="form-control" required>
                    @foreach($grados as $g)
                    <option value="{{ $g->id }}" data-secciones="{{ $g->secciones->pluck('id')->join(',') }}"
                        {{ old('grado_id',$matricula->grado_id)==$g->id ? 'selected':'' }}>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Sección *</label>
                <select name="seccion_id" id="seccion_select" class="form-control" required>
                    @foreach($secciones as $s)
                    <option value="{{ $s->id }}" data-grado="{{ $s->grado_id }}"
                        {{ old('seccion_id',$matricula->seccion_id)==$s->id ? 'selected':'' }}>
                        Sección {{ $s->nombre }} ({{ ucfirst($s->turno) }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Año Escolar</label>
                <select name="anio_escolar" class="form-control">
                    @for($y=date('Y');$y>=2020;$y--)
                    <option value="{{ $y }}" {{ old('anio_escolar',$matricula->anio_escolar)==$y ? 'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha Matrícula</label>
                <input type="date" name="fecha_matricula" class="form-control"
                    value="{{ old('fecha_matricula', $matricula->fecha_matricula?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Estado *</label>
                <select name="estado" class="form-control" required>
                    @foreach(['activo','retirado','trasladado'] as $e)
                    <option value="{{ $e }}" {{ old('estado',$matricula->estado)===$e ? 'selected':'' }}>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $matricula->observaciones) }}</textarea>
        </div>
    </div>
</div>

<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
    <a href="{{ route('matriculas.show', $matricula) }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
</div>
</form>
</div>
@endsection

@push('scripts')
<script>
const gradoSelect   = document.getElementById('grado_select');
const seccionSelect = document.getElementById('seccion_select');
const allOpts       = Array.from(seccionSelect.options);

gradoSelect.addEventListener('change', function() {
    const gId = this.value;
    seccionSelect.innerHTML = '';
    allOpts.filter(o => o.dataset.grado == gId).forEach(o => seccionSelect.add(o.cloneNode(true)));
    if (!seccionSelect.options.length) {
        const ph = document.createElement('option'); ph.text = '— Sin secciones —'; seccionSelect.add(ph);
    }
});
if (gradoSelect.value) gradoSelect.dispatchEvent(new Event('change'));
</script>
@endpush
