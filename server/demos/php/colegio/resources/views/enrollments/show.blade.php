@extends('layouts.app')
@section('title', 'Detalle de Matrícula')
@section('page-title', 'Detalle de Matrícula')

@section('content')

<div style="display:flex;gap:12px;margin-bottom:20px;">
    <a href="{{ route('matriculas.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    <a href="{{ route('matriculas.edit', $matricula) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
    <a href="{{ route('alumnos.show', $matricula->alumno) }}" class="btn btn-secondary"><i class="fas fa-user-graduate"></i> Ver Alumno</a>
</div>

<div class="grid grid-2" style="gap:20px;">

    {{-- Datos de la matrícula --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-file-signature" style="color:#3b82f6;margin-right:8px;"></i>Datos de la Matrícula</span>
            <span class="badge {{ $matricula->estado==='activo' ? 'badge-success' : ($matricula->estado==='retirado' ? 'badge-danger' : 'badge-warning') }}">
                {{ ucfirst($matricula->estado) }}
            </span>
        </div>
        <div class="card-body" style="padding:0;">
            @foreach([
                ['fas fa-hashtag','N° Matrícula', $matricula->numero],
                ['fas fa-calendar','Año Escolar', $matricula->anio_escolar],
                ['fas fa-graduation-cap','Grado', $matricula->grado->nombre ?? '—'],
                ['fas fa-door-open','Sección', 'Sección '.($matricula->seccion->nombre ?? '—').' · '.ucfirst($matricula->seccion->turno ?? '')],
                ['fas fa-calendar-day','Fecha de Matrícula', $matricula->fecha_matricula?->format('d/m/Y')],
                ['fas fa-user-check','Registrado por', $matricula->registradoPor->name ?? '—'],
            ] as [$icon, $label, $val])
            <div style="display:flex;align-items:center;gap:14px;padding:14px 22px;border-bottom:1px solid var(--border);">
                <div style="width:34px;height:34px;border-radius:9px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:var(--muted);flex-shrink:0;">
                    <i class="{{ $icon }}"></i>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);">{{ $label }}</div>
                    <div style="font-weight:600;">{{ $val }}</div>
                </div>
            </div>
            @endforeach
            @if($matricula->observaciones)
            <div style="padding:14px 22px;">
                <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Observaciones</div>
                <div style="font-size:13.5px;">{{ $matricula->observaciones }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Datos del alumno --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-user-graduate" style="color:#10b981;margin-right:8px;"></i>Datos del Alumno</span>
        </div>
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border);">
                <div style="width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);display:flex;align-items:center;justify-content:center;color:white;font-size:24px;font-weight:800;">
                    {{ strtoupper(substr($matricula->alumno->nombres??'',0,1)) }}{{ strtoupper(substr($matricula->alumno->apellidos??'',0,1)) }}
                </div>
                <div>
                    <div style="font-size:17px;font-weight:700;">{{ $matricula->alumno->nombre_completo ?? '—' }}</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $matricula->alumno->codigo ?? '' }}</div>
                    <div style="font-size:12px;color:var(--muted);">DNI: {{ $matricula->alumno->dni ?? '—' }}</div>
                </div>
            </div>
            @foreach([
                ['fas fa-venus-mars','Género', $matricula->alumno->genero==='M'?'Masculino':'Femenino'],
                ['fas fa-birthday-cake','Fecha Nac.', $matricula->alumno->fecha_nacimiento?->format('d/m/Y')],
                ['fas fa-phone','Teléfono', $matricula->alumno->telefono ?? '—'],
                ['fas fa-user-shield','Apoderado', $matricula->alumno->apoderado_nombre ?? '—'],
                ['fas fa-mobile','Tel. Apoderado', $matricula->alumno->apoderado_telefono ?? '—'],
            ] as [$icon, $label, $val])
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                <i class="{{ $icon }}" style="color:var(--muted);width:16px;text-align:center;"></i>
                <span style="font-size:12px;color:var(--muted);min-width:100px;">{{ $label }}</span>
                <span style="font-size:13.5px;font-weight:500;">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
