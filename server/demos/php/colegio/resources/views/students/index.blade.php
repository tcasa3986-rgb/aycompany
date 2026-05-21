@extends('layouts.app')
@section('title', 'Alumnos')
@section('page-title', 'Gestión de Alumnos')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div>
        <p style="color:var(--muted);font-size:13px;">Total: <strong>{{ $alumnos->total() }}</strong> alumnos encontrados</p>
    </div>
    <a href="{{ route('alumnos.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Alumno
    </a>
</div>

{{-- Filtros --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 22px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <label class="form-label" style="margin-bottom:4px;">Buscar</label>
                <div style="position:relative;">
                    <i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);"></i>
                    <input type="text" name="buscar" class="form-control" style="padding-left:34px;"
                        placeholder="Nombre, apellido o DNI..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div style="min-width:160px;">
                <label class="form-label" style="margin-bottom:4px;">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="activo"     {{ request('estado')=='activo'     ? 'selected':'' }}>Activo</option>
                    <option value="inactivo"   {{ request('estado')=='inactivo'   ? 'selected':'' }}>Inactivo</option>
                    <option value="trasladado" {{ request('estado')=='trasladado' ? 'selected':'' }}>Trasladado</option>
                    <option value="egresado"   {{ request('estado')=='egresado'   ? 'selected':'' }}>Egresado</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Alumno</th>
                    <th>DNI</th>
                    <th>Grado / Sección</th>
                    <th>Apoderado</th>
                    <th>Estado</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($alumnos as $alumno)
                @php $matricula = $alumno->matriculaActiva(); @endphp
                <tr>
                    <td>
                        <span style="font-family:monospace;font-size:12px;background:#f1f5f9;padding:3px 8px;border-radius:6px;">
                            {{ $alumno->codigo }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0;">
                                {{ strtoupper(substr($alumno->nombres,0,1)) }}{{ strtoupper(substr($alumno->apellidos,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;">{{ $alumno->nombre_completo }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $alumno->genero === 'M' ? 'Masculino' : 'Femenino' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-family:monospace;">{{ $alumno->dni }}</td>
                    <td>
                        @if($matricula)
                            <span style="font-size:13px;">{{ $matricula->grado->nombre ?? '—' }}</span>
                            <span style="font-size:11px;color:var(--muted);"> / Sec. {{ $matricula->seccion->nombre ?? '—' }}</span>
                        @else
                            <span style="color:var(--muted);font-size:12px;">Sin matrícula</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:13px;">{{ $alumno->apoderado_nombre ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $alumno->apoderado_telefono ?? '' }}</div>
                    </td>
                    <td>
                        @php
                            $estadoClass = match($alumno->estado) {
                                'activo'     => 'badge-success',
                                'inactivo'   => 'badge-secondary',
                                'trasladado' => 'badge-warning',
                                'egresado'   => 'badge-info',
                                default      => 'badge-secondary'
                            };
                        @endphp
                        <span class="badge {{ $estadoClass }}">{{ ucfirst($alumno->estado) }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:center;">
                            <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-sm btn-secondary btn-icon" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-sm btn-secondary btn-icon" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('alumnos.destroy', $alumno) }}"
                                  onsubmit="return confirm('¿Desactivar este alumno?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Desactivar">
                                    <i class="fas fa-user-slash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--muted);">
                        <i class="fas fa-user-graduate" style="font-size:36px;margin-bottom:12px;display:block;opacity:.3;"></i>
                        No se encontraron alumnos.
                        <br><a href="{{ route('alumnos.create') }}" style="color:var(--primary-l);">Registrar el primero</a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($alumnos->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <div style="font-size:13px;color:var(--muted);">
            Mostrando {{ $alumnos->firstItem() }}–{{ $alumnos->lastItem() }} de {{ $alumnos->total() }}
        </div>
        {{ $alumnos->links() }}
    </div>
    @endif
</div>

@endsection
