@extends('layouts.app')

@section('title', 'Nuevo Paciente')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Nuevo Paciente</h1>
        <p class="text-secondary">Registro de nueva historia clínica</p>
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

    <form action="{{ route('pacientes.store') }}" method="POST">
        @csrf
        
        <div class="dashboard-grid">
            <div class="col-4 form-group">
                <label class="form-label">Tipo Documento</label>
                <select name="tipo_documento" class="form-control" required>
                    <option value="DNI">DNI</option>
                    <option value="CE">Carnet de Extranjería</option>
                    <option value="Pasaporte">Pasaporte</option>
                </select>
            </div>
            <div class="col-8 form-group">
                <label class="form-label">Número de Documento</label>
                <input type="text" name="numero_documento" class="form-control" value="{{ old('numero_documento') }}" required>
            </div>

            <div class="col-4 form-group">
                <label class="form-label">Nombres</label>
                <input type="text" name="nombres" class="form-control" value="{{ old('nombres') }}" required>
            </div>
            <div class="col-4 form-group">
                <label class="form-label">Apellido Paterno</label>
                <input type="text" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno') }}" required>
            </div>
            <div class="col-4 form-group">
                <label class="form-label">Apellido Materno</label>
                <input type="text" name="apellido_materno" class="form-control" value="{{ old('apellido_materno') }}">
            </div>

            <div class="col-4 form-group">
                <label class="form-label">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
            </div>
            <div class="col-4 form-group">
                <label class="form-label">Sexo</label>
                <select name="sexo" class="form-control">
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
            <div class="col-4 form-group">
                <label class="form-label">Tipo de Sangre</label>
                <select name="tipo_sangre" class="form-control">
                    <option value="">Desconocido</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
            </div>

            <div class="col-6 form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="col-6 form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
            </div>

            <div class="col-12 form-group">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}">
            </div>

            <div class="col-12" style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar Paciente</button>
            </div>
        </div>
    </form>
</div>
@endsection
