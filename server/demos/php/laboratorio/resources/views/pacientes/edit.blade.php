@extends('layouts.app')

@section('title', 'Editar Paciente')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Editar Paciente</h1>
        <p class="text-secondary">Actualizar registro de la historia clínica: {{ $paciente->historia_clinica }}</p>
    </div>
    <div>
        <a href="{{ route('pacientes.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: white;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
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

    <form action="{{ route('pacientes.update', $paciente->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="dashboard-grid">
            <div class="col-4 form-group">
                <label class="form-label">Tipo Documento</label>
                <select name="tipo_documento" class="form-control" required>
                    <option value="DNI" {{ old('tipo_documento', $paciente->tipo_documento) == 'DNI' ? 'selected' : '' }}>DNI</option>
                    <option value="CE" {{ old('tipo_documento', $paciente->tipo_documento) == 'CE' ? 'selected' : '' }}>Carnet de Extranjería</option>
                    <option value="Pasaporte" {{ old('tipo_documento', $paciente->tipo_documento) == 'Pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                </select>
            </div>
            <div class="col-8 form-group">
                <label class="form-label">Número de Documento</label>
                <input type="text" name="numero_documento" class="form-control" value="{{ old('numero_documento', $paciente->numero_documento) }}" required>
            </div>

            <div class="col-4 form-group">
                <label class="form-label">Nombres</label>
                <input type="text" name="nombres" class="form-control" value="{{ old('nombres', $paciente->nombres) }}" required>
            </div>
            <div class="col-4 form-group">
                <label class="form-label">Apellido Paterno</label>
                <input type="text" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno', $paciente->apellido_paterno) }}" required>
            </div>
            <div class="col-4 form-group">
                <label class="form-label">Apellido Materno</label>
                <input type="text" name="apellido_materno" class="form-control" value="{{ old('apellido_materno', $paciente->apellido_materno) }}">
            </div>

            <div class="col-4 form-group">
                <label class="form-label">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento ? $paciente->fecha_nacimiento->format('Y-m-d') : '') }}">
            </div>
            <div class="col-4 form-group">
                <label class="form-label">Sexo</label>
                <select name="sexo" class="form-control">
                    <option value="M" {{ old('sexo', $paciente->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('sexo', $paciente->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>
            <div class="col-4 form-group">
                <label class="form-label">Tipo de Sangre</label>
                <select name="tipo_sangre" class="form-control">
                    <option value="" {{ old('tipo_sangre', $paciente->tipo_sangre) == '' ? 'selected' : '' }}>Desconocido</option>
                    <option value="O+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'O-' ? 'selected' : '' }}>O-</option>
                    <option value="A+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'AB-' ? 'selected' : '' }}>AB-</option>
                </select>
            </div>

            <div class="col-6 form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $paciente->email) }}">
            </div>
            <div class="col-6 form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $paciente->telefono) }}">
            </div>

            <div class="col-12 form-group">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $paciente->direccion) }}">
            </div>

            <div class="col-12" style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Actualizar Paciente</button>
            </div>
        </div>
    </form>
</div>
@endsection
