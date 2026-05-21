@extends('layouts.app')
@section('title', 'Personal')
@section('page-title', 'Gestión de Personal')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <p style="color:var(--muted);font-size:13px;">{{ $personal->total() }} registros encontrados</p>
    <a href="{{ route('personal.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Personal</a>
</div>

{{-- Filtros --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 22px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <input type="text" name="buscar" class="form-control"
                    placeholder="Nombre, apellido o DNI..." value="{{ request('buscar') }}">
            </div>
            <select name="tipo" class="form-control" style="min-width:160px;">
                <option value="">Todos los tipos</option>
                <option value="docente"        {{ request('tipo')=='docente'        ? 'selected':'' }}>Docente</option>
                <option value="administrativo" {{ request('tipo')=='administrativo' ? 'selected':'' }}>Administrativo</option>
                <option value="directivo"      {{ request('tipo')=='directivo'      ? 'selected':'' }}>Directivo</option>
                <option value="auxiliar"       {{ request('tipo')=='auxiliar'       ? 'selected':'' }}>Auxiliar</option>
            </select>
            <select name="estado" class="form-control" style="min-width:140px;">
                <option value="">Todos los estados</option>
                <option value="activo"   {{ request('estado')=='activo'   ? 'selected':'' }}>Activo</option>
                <option value="inactivo" {{ request('estado')=='inactivo' ? 'selected':'' }}>Inactivo</option>
                <option value="licencia" {{ request('estado')=='licencia' ? 'selected':'' }}>Licencia</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('personal.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Personal</th>
                    <th>DNI</th>
                    <th>Tipo</th>
                    <th>Especialidad</th>
                    <th>Teléfono</th>
                    <th>Ingreso</th>
                    <th>Salario</th>
                    <th>Estado</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($personal as $p)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#065f46,#10b981);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0;">
                                {{ strtoupper(substr($p->nombres,0,1)) }}{{ strtoupper(substr($p->apellidos,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;">{{ $p->nombre_completo }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $p->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-family:monospace;">{{ $p->dni }}</td>
                    <td><span class="badge badge-{{ $p->tipo_badge }}">{{ ucfirst($p->tipo) }}</span></td>
                    <td style="font-size:13px;color:var(--muted);">{{ $p->especialidad ?? '—' }}</td>
                    <td>{{ $p->telefono ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $p->fecha_ingreso?->format('d/m/Y') }}</td>
                    <td style="font-weight:700;">S/. {{ number_format($p->salario, 2) }}</td>
                    <td>
                        @php $ec = $p->estado === 'activo' ? 'badge-success' : ($p->estado === 'licencia' ? 'badge-warning' : 'badge-secondary'); @endphp
                        <span class="badge {{ $ec }}">{{ ucfirst($p->estado) }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:center;">
                            <a href="{{ route('personal.show', $p) }}" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('personal.edit', $p) }}" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('personal.destroy', $p) }}" onsubmit="return confirm('¿Desactivar?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-icon"><i class="fas fa-user-slash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px;color:var(--muted);">
                        <i class="fas fa-users" style="font-size:36px;margin-bottom:12px;display:block;opacity:.3;"></i>
                        No se encontró personal registrado.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($personal->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--border);">
        {{ $personal->links() }}
    </div>
    @endif
</div>

@endsection
