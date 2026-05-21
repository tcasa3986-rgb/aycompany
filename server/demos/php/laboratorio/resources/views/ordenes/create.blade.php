@extends('layouts.app')

@section('title', 'Nueva Orden Médica')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Nueva Orden Médica</h1>
        <p class="text-secondary">Registro de pruebas para paciente</p>
    </div>
    <div>
        <a href="{{ route('ordenes.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: white;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
    </div>
</div>

<div class="card" style="max-width: 1000px; margin: 0 auto;">
    @if ($errors->any())
        <div class="alert-error" style="background: rgba(255, 71, 87, 0.1); color: var(--danger); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">
            <ul style="margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ordenes.store') }}" method="POST">
        @csrf
        
        <div class="dashboard-grid">
            <div class="col-6 form-group">
                <label class="form-label">Paciente *</label>
                <select name="paciente_id" class="form-control" required>
                    <option value="">Seleccione paciente...</option>
                    @foreach($pacientes as $paciente)
                        <option value="{{ $paciente->id }}">{{ $paciente->tipo_documento }} {{ $paciente->numero_documento }} - {{ $paciente->nombre_completo }}</option>
                    @endforeach
                </select>
                <small><a href="{{ route('pacientes.create') }}" class="text-info"><i class="fa-solid fa-plus"></i> Registrar nuevo paciente</a></small>
            </div>
            
            <div class="col-6 form-group">
                <label class="form-label">Médico Referidor</label>
                <select name="medico_id" class="form-control">
                    <option value="">Ninguno</option>
                    @foreach($medicos as $medico)
                        <option value="{{ $medico->id }}">{{ $medico->nombre_completo }} ({{ $medico->especialidad }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 form-group">
                <label class="form-label">Diagnóstico Presuntivo</label>
                <input type="text" name="diagnostico_presuntivo" class="form-control" placeholder="Breve observación clínica..." value="{{ old('diagnostico_presuntivo') }}">
            </div>

            <div class="col-4 form-group">
                <label class="form-label">Convenio / Aseguradora</label>
                <select name="convenio_id" class="form-control">
                    <option value="">Particular (Sin convenio)</option>
                    @foreach($convenios as $convenio)
                        <option value="{{ $convenio->id }}">{{ $convenio->nombre }} (-{{ $convenio->descuento_porcentaje }}%)</option>
                    @endforeach
                </select>
            </div>

            <div class="col-4 form-group">
                <label class="form-label">Prioridad *</label>
                <select name="prioridad" class="form-control" required>
                    <option value="Normal">Normal</option>
                    <option value="Urgente">Urgente</option>
                    <option value="Emergencia">Emergencia</option>
                </select>
            </div>

            <!-- Pruebas Section -->
            <div class="col-12" style="margin-top: 20px;">
                <h3 style="margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Selección de Pruebas Clínicas</h3>
                
                <div style="max-height: 400px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 15px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    @foreach($pruebas->groupBy('area_id') as $areaId => $pruebasArea)
                        <div style="margin-bottom: 15px;">
                            <h4 style="color: var(--accent-secondary); margin-bottom: 10px;">{{ $pruebasArea->first()->area->nombre }}</h4>
                            <div class="dashboard-grid">
                            @foreach($pruebasArea as $prueba)
                                <div class="col-4" style="margin-bottom: 10px;">
                                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--text-primary);">
                                        <input type="checkbox" name="pruebas[]" value="{{ $prueba->id }}" style="width: 18px; height: 18px;">
                                        <div style="display:flex; flex-direction: column;">
                                            <span>{{ $prueba->codigo }} - {{ $prueba->nombre }}</span>
                                            <span style="font-size: 0.8rem; color: var(--text-muted);">S/ {{ number_format($prueba->precio, 2) }}</span>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-12" style="margin-top: 30px; text-align: right; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-file-invoice"></i> Generar Orden Médica</button>
            </div>
        </div>
    </form>
</div>
@endsection
