@extends('layouts.app')

@section('title', 'Editar Prueba Analítica')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Editar Prueba</h1>
        <p class="text-secondary">Modificar servicio: {{ $prueba->codigo }}</p>
    </div>
    <div>
        <a href="{{ route('pruebas.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: white;"><i class="fa-solid fa-arrow-left"></i> Cancelar</a>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    @if ($errors->any())
        <div class="alert-error" style="background: rgba(255, 71, 87, 0.1); color: var(--danger); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">
            <ul style="margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pruebas.update', $prueba->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="dashboard-grid">
            <div class="col-4 form-group">
                <label class="form-label">Código Interno</label>
                <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $prueba->codigo) }}" required>
            </div>
            
            <div class="col-8 form-group">
                <label class="form-label">Nombre de la Prueba</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $prueba->nombre) }}" required>
            </div>

            <div class="col-6 form-group">
                <label class="form-label">Área de Laboratorio</label>
                <select name="area_id" class="form-control" required>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id', $prueba->area_id) == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 form-group">
                <label class="form-label">Tipo de Muestra</label>
                <input type="text" name="muestra_tipo" class="form-control" value="{{ old('muestra_tipo', $prueba->muestra_tipo) }}" required>
            </div>

            <div class="col-4 form-group">
                <label class="form-label">Precio (S/)</label>
                <input type="number" name="precio" class="form-control" step="0.01" value="{{ old('precio', $prueba->precio) }}" required style="font-size: 1.2rem; font-weight:bold; color: var(--success);">
            </div>

            <div class="col-4 form-group">
                <label class="form-label">Tiempo Cierre (Días)</label>
                <input type="number" name="tiempo_resultado" class="form-control" value="{{ old('tiempo_resultado', $prueba->tiempo_resultado) }}" required>
            </div>
            
            <div class="col-4 form-group">
                <label class="form-label">Unidad de Medida</label>
                <input type="text" name="unidad" class="form-control" value="{{ old('unidad', $prueba->unidad) }}">
            </div>

            <div class="col-12 form-group">
                <label class="form-label">Valores de Referencia</label>
                <textarea name="valores_referencia" class="form-control" rows="3">{{ old('valores_referencia', $prueba->valores_referencia) }}</textarea>
            </div>

            <div class="col-12 form-group">
                <label class="form-label" style="display:flex; align-items:center; gap: 10px; cursor:pointer;">
                    <input type="checkbox" name="activo" value="1" {{ old('activo', $prueba->activo) ? 'checked' : '' }} style="width:18px;height:18px;">
                    Prueba Disponible para Facturación / Activa
                </label>
            </div>

            <div class="col-12" style="margin-top: 20px; text-align: right; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Actualizar en Catálogo</button>
            </div>
        </div>
    </form>
</div>
@endsection
