@extends('layouts.app')
@section('title', 'Ficha del Alumno')
@section('page-title', 'Ficha del Alumno')

@section('content')

<div style="display:flex;gap:12px;align-items:center;margin-bottom:20px;">
    <a href="{{ route('alumnos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
    <a href="{{ route('matriculas.create') }}?alumno_id={{ $alumno->id }}" class="btn btn-success"><i class="fas fa-file-signature"></i> Nueva Matrícula</a>
    <a href="{{ route('pagos.create') }}?alumno_id={{ $alumno->id }}" class="btn btn-success"><i class="fas fa-money-bill"></i> Registrar Pago</a>
</div>

<div class="grid grid-3" style="gap:20px;">

    {{-- Columna izquierda: datos personales --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Perfil --}}
        <div class="card" style="text-align:center;padding:28px 20px;">
            <div style="width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);display:flex;align-items:center;justify-content:center;color:white;font-size:32px;font-weight:800;margin:0 auto 16px;">
                {{ strtoupper(substr($alumno->nombres,0,1)) }}{{ strtoupper(substr($alumno->apellidos,0,1)) }}
            </div>
            <div style="font-size:18px;font-weight:700;">{{ $alumno->nombre_completo }}</div>
            <div style="font-size:13px;color:var(--muted);margin-top:4px;">{{ $alumno->codigo }}</div>

            @php $estadoClass = match($alumno->estado) { 'activo'=>'badge-success','inactivo'=>'badge-secondary','trasladado'=>'badge-warning','egresado'=>'badge-info',default=>'badge-secondary' }; @endphp
            <span class="badge {{ $estadoClass }}" style="margin-top:12px;">{{ ucfirst($alumno->estado) }}</span>

            @php $mat = $alumno->matriculaActiva(); @endphp
            @if($mat)
            <div style="margin-top:16px;padding:12px;background:#f0f7ff;border-radius:10px;">
                <div style="font-size:12px;color:var(--muted);">Grado actual</div>
                <div style="font-weight:700;color:#1e3a8a;">{{ $mat->grado->nombre ?? '—' }}</div>
                <div style="font-size:12px;color:var(--muted);">Sección {{ $mat->seccion->nombre ?? '—' }} · {{ $mat->anio_escolar }}</div>
            </div>
            @endif
        </div>

        {{-- Datos personales --}}
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-id-card" style="color:#3b82f6;margin-right:8px;"></i>Datos Personales</span></div>
            <div class="card-body" style="padding:0;">
                @foreach([
                    ['fas fa-calendar','Fecha de Nacimiento', $alumno->fecha_nacimiento?->format('d/m/Y').' ('.($alumno->fecha_nacimiento?->age).' años)'],
                    ['fas fa-venus-mars','Género', $alumno->genero === 'M' ? 'Masculino' : 'Femenino'],
                    ['fas fa-id-badge','DNI', $alumno->dni],
                    ['fas fa-phone','Teléfono', $alumno->telefono ?? '—'],
                    ['fas fa-envelope','Email', $alumno->email ?? '—'],
                    ['fas fa-map-marker-alt','Dirección', $alumno->direccion ?? '—'],
                ] as [$icon, $label, $val])
                <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid var(--border);">
                    <div style="width:32px;height:32px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:13px;flex-shrink:0;">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--muted);">{{ $label }}</div>
                        <div style="font-size:13.5px;font-weight:500;">{{ $val }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Apoderado --}}
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-user-friends" style="color:#10b981;margin-right:8px;"></i>Apoderado</span></div>
            <div class="card-body" style="padding:0;">
                @foreach([
                    ['fas fa-user','Nombre', $alumno->apoderado_nombre ?? '—'],
                    ['fas fa-link','Parentesco', $alumno->apoderado_parentesco ?? '—'],
                    ['fas fa-id-badge','DNI', $alumno->apoderado_dni ?? '—'],
                    ['fas fa-phone','Teléfono', $alumno->apoderado_telefono ?? '—'],
                    ['fas fa-envelope','Email', $alumno->apoderado_email ?? '—'],
                ] as [$icon, $label, $val])
                <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid var(--border);">
                    <div style="width:32px;height:32px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:13px;flex-shrink:0;">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--muted);">{{ $label }}</div>
                        <div style="font-size:13.5px;font-weight:500;">{{ $val }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Columnas derecha: historial --}}
    <div style="grid-column:span 2;display:flex;flex-direction:column;gap:20px;">

        {{-- Matrículas --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-file-signature" style="color:#3b82f6;margin-right:8px;"></i>Historial de Matrículas</span>
                <span class="badge badge-info">{{ $alumno->matriculas->count() }}</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>N° Matrícula</th><th>Año</th><th>Grado</th><th>Sección</th><th>Fecha</th><th>Estado</th><th></th></tr></thead>
                    <tbody>
                    @forelse($alumno->matriculas->sortByDesc('anio_escolar') as $m)
                        <tr>
                            <td style="font-family:monospace;font-size:12px;">{{ $m->numero }}</td>
                            <td style="font-weight:700;">{{ $m->anio_escolar }}</td>
                            <td>{{ $m->grado->nombre ?? '—' }}</td>
                            <td>Sec. {{ $m->seccion->nombre ?? '—' }}</td>
                            <td style="font-size:12px;">{{ $m->fecha_matricula?->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $m->estado==='activo'?'badge-success':($m->estado==='retirado'?'badge-danger':'badge-warning') }}">{{ ucfirst($m->estado) }}</span></td>
                            <td><a href="{{ route('matriculas.show', $m) }}" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;padding:24px;color:var(--muted);">Sin matrículas registradas</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Estado de cuenta --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-receipt" style="color:#10b981;margin-right:8px;"></i>Estado de Cuenta</span>
                <div style="display:flex;gap:12px;align-items:center;">
                    @php
                        $totalPagado   = $alumno->pagos->where('estado','pagado')->sum('monto_pagado');
                        $totalPendiente= $alumno->pagos->where('estado','pendiente')->sum('monto');
                    @endphp
                    <span style="font-size:12px;color:var(--success);font-weight:700;">Pagado: S/. {{ number_format($totalPagado,2) }}</span>
                    <span style="font-size:12px;color:var(--danger);font-weight:700;">Pendiente: S/. {{ number_format($totalPendiente,2) }}</span>
                </div>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Recibo</th><th>Concepto</th><th>Mes / Año</th><th>Monto</th><th>Fecha</th><th>Método</th><th>Estado</th><th></th></tr></thead>
                    <tbody>
                    @forelse($alumno->pagos->sortByDesc('fecha_pago') as $pago)
                        <tr>
                            <td style="font-family:monospace;font-size:12px;">{{ $pago->numero_recibo }}</td>
                            <td>{{ $pago->concepto->nombre ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--muted);">{{ $pago->nombre_mes }} {{ $pago->anio_escolar }}</td>
                            <td style="font-weight:700;">S/. {{ number_format($pago->monto_pagado,2) }}</td>
                            <td style="font-size:12px;">{{ $pago->fecha_pago?->format('d/m/Y') }}</td>
                            <td style="font-size:12px;text-transform:capitalize;">{{ $pago->metodo_pago }}</td>
                            <td><span class="badge badge-{{ $pago->estado_badge }}">{{ ucfirst($pago->estado) }}</span></td>
                            <td><a href="{{ route('pagos.show', $pago) }}" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align:center;padding:24px;color:var(--muted);">Sin pagos registrados</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection
