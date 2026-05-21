@extends('layouts.app')
@section('title', 'Nueva Matrícula')
@section('page-title', 'Registrar Matrícula')

@section('content')
<div style="max-width:760px;">
<div style="margin-bottom:16px;">
    <a href="{{ route('matriculas.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<form method="POST" action="{{ route('matriculas.store') }}">
@csrf
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-file-signature" style="color:#3b82f6;margin-right:8px;"></i>Datos de la Matrícula</span>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Alumno *</label>
            <select name="alumno_id" class="form-control" required>
                <option value="">Seleccionar alumno...</option>
                @foreach($alumnos as $a)
                    <option value="{{ $a->id }}" {{ old('alumno_id')==$a->id ? 'selected':'' }}>
                        {{ $a->nombre_completo }} — DNI: {{ $a->dni }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Grado *</label>
                <select name="grado_id" id="grado_select" class="form-control" required>
                    <option value="">Seleccionar grado...</option>
                    @foreach($grados as $g)
                        <option value="{{ $g->id }}" {{ old('grado_id')==$g->id ? 'selected':'' }}>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Sección *</label>
                <select name="seccion_id" id="seccion_select" class="form-control" required>
                    <option value="">Primero selecciona un grado</option>
                    @foreach($secciones as $s)
                        <option value="{{ $s->id }}" data-grado="{{ $s->grado_id }}" {{ old('seccion_id')==$s->id ? 'selected':'' }}>
                            Sección {{ $s->nombre }} ({{ ucfirst($s->turno) }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Año Escolar *</label>
                <select name="anio_escolar" class="form-control" required>
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ old('anio_escolar', date('Y'))==$y ? 'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha de Matrícula *</label>
                <input type="date" name="fecha_matricula" class="form-control"
                    value="{{ old('fecha_matricula', date('Y-m-d')) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones') }}</textarea>
        </div>
    </div>
</div>

<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
    <a href="{{ route('matriculas.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Matrícula</button>
</div>
</form>
</div>
@endsection

@push('scripts')
<script>
// Filtrar secciones según el grado seleccionado
const gradoSelect   = document.getElementById('grado_select');
const seccionSelect = document.getElementById('seccion_select');
const allOpts       = Array.from(seccionSelect.options);

gradoSelect.addEventListener('change', function() {
    const gId = this.value;
    seccionSelect.innerHTML = '';
    const placeholder = document.createElement('option');
    placeholder.text  = 'Seleccionar sección...';
    placeholder.value = '';
    seccionSelect.add(placeholder);

    allOpts.filter(o => o.dataset.grado == gId || !o.value).forEach(o => {
        if (o.dataset.grado == gId) seccionSelect.add(o.cloneNode(true));
    });
});

// Disparar cambio si hay valor previo
if (gradoSelect.value) gradoSelect.dispatchEvent(new Event('change'));
</script>
@endpush
