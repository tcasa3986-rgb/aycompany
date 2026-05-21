@extends('layouts.app')

@section('title', 'Pacientes')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Pacientes</h1>
        <p class="text-secondary">Gestión de historias clínicas y registro</p>
    </div>
    <div>
        <a href="{{ route('pacientes.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo Paciente</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('pacientes.index') }}" method="GET" class="d-flex" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Buscar por DNI o Nombres..." value="{{ request('search') }}" style="width: 300px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i></button>
        </form>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Historia Clínica</th>
                    <th>Documento</th>
                    <th>Paciente</th>
                    <th>Edad</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pacientes as $paciente)
                <tr>
                    <td><strong>{{ $paciente->historia_clinica }}</strong></td>
                    <td>{{ $paciente->tipo_documento }} {{ $paciente->numero_documento }}</td>
                    <td>{{ $paciente->nombre_completo }}</td>
                    <td>{{ $paciente->edad ? $paciente->edad . ' años' : 'N/A' }}</td>
                    <td>{{ $paciente->telefono ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('pacientes.edit', $paciente->id) }}" class="action-btn text-info" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No se encontraron pacientes.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pacientes->hasPages())
    <div style="margin-top: 20px;">
        {{ $pacientes->links() }}
    </div>
    @endif
</div>
@endsection
