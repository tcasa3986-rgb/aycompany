@extends('layouts.app')
@section('title', 'Secciones — '.$grado->nombre)
@section('page-title', 'Secciones de '.$grado->nombre)

@section('content')

<div style="display:flex;gap:12px;margin-bottom:20px;align-items:center;">
    <a href="{{ route('grados.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Grados</a>
    <span class="badge badge-primary" style="font-size:13px;padding:8px 14px;">{{ ucfirst($grado->nivel) }}</span>
</div>

<div class="grid grid-2" style="gap:24px;align-items:start;">

    {{-- Lista de secciones --}}
    <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <h3 style="font-size:15px;font-weight:700;">Secciones ({{ $grado->secciones->count() }})</h3>
            <button onclick="document.getElementById('modal-sec').style.display='flex'" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva Sección
            </button>
        </div>

        @forelse($grado->secciones as $sec)
        <div class="card" style="margin-bottom:12px;padding:0;overflow:hidden;">
            <div style="display:flex;align-items:center;gap:0;">
                <div style="width:6px;height:100%;background:linear-gradient(180deg,#1e3a8a,#3b82f6);min-height:80px;flex-shrink:0;"></div>
                <div style="flex:1;padding:16px 18px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <span style="font-size:18px;font-weight:800;color:var(--primary);">Sección {{ $sec->nombre }}</span>
                            <span class="badge badge-secondary" style="margin-left:10px;">{{ ucfirst($sec->turno) }}</span>
                        </div>
                        <div style="display:flex;gap:6px;">
                            <button onclick="editarSeccion({{ $sec->id }},'{{ $sec->nombre }}','{{ $sec->turno }}',{{ $sec->capacidad }})"
                                class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-edit"></i></button>
                            @if($sec->matriculas->count() == 0)
                            <form method="POST" action="{{ route('secciones.destroy', $sec) }}" onsubmit="return confirm('¿Eliminar sección?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-icon"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;gap:20px;margin-top:8px;">
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fas fa-users"></i>
                            <strong>{{ $sec->matriculas->count() }}</strong>/{{ $sec->capacidad }} alumnos
                        </div>
                        <div style="font-size:12px;">
                            @php $pct = $sec->capacidad > 0 ? min(100, ($sec->matriculas->count()/$sec->capacidad)*100) : 0; @endphp
                            <div style="width:100px;height:6px;background:#e2e8f0;border-radius:3px;display:inline-block;vertical-align:middle;">
                                <div style="width:{{ $pct }}%;height:100%;background:{{ $pct>90?'#ef4444':($pct>70?'#f59e0b':'#10b981') }};border-radius:3px;"></div>
                            </div>
                            <span style="color:var(--muted);margin-left:4px;">{{ round($pct) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card" style="padding:32px;text-align:center;color:var(--muted);">
            <i class="fas fa-door-open" style="font-size:32px;opacity:.3;display:block;margin-bottom:12px;"></i>
            Sin secciones registradas para este grado.
        </div>
        @endforelse
    </div>

    {{-- Docentes disponibles --}}
    <div>
        <div style="margin-bottom:14px;">
            <h3 style="font-size:15px;font-weight:700;">Docentes Activos</h3>
        </div>
        <div class="card">
            <div style="padding:12px 0;max-height:400px;overflow-y:auto;">
                @foreach($docentes as $d)
                <div style="display:flex;align-items:center;gap:12px;padding:10px 18px;border-bottom:1px solid var(--border);">
                    <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#065f46,#10b981);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:12px;flex-shrink:0;">
                        {{ strtoupper(substr($d->nombres,0,1))  }}{{ strtoupper(substr($d->apellidos,0,1)) }}
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;">{{ $d->nombre_completo }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $d->especialidad ?? 'Sin especialidad' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- Modal Sección --}}
<div id="modal-sec" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:16px;padding:28px;width:420px;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <div style="display:flex;justify-content:space-between;margin-bottom:20px;">
            <h3 id="modal-sec-title" style="font-size:16px;font-weight:700;">Nueva Sección</h3>
            <button onclick="document.getElementById('modal-sec').style.display='none'" style="background:none;border:none;font-size:18px;cursor:pointer;color:var(--muted);">✕</button>
        </div>
        <form id="form-sec" method="POST" action="{{ route('grados.secciones.store', $grado) }}">
            @csrf
            <input type="hidden" name="_method" id="sec-method" value="POST">
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Nombre (letra) *</label>
                    <input type="text" name="nombre" id="sec-nombre" class="form-control" maxlength="10" required placeholder="A, B, C...">
                </div>
                <div class="form-group">
                    <label class="form-label">Turno *</label>
                    <select name="turno" id="sec-turno" class="form-control" required>
                        <option value="mañana">Mañana</option>
                        <option value="tarde">Tarde</option>
                        <option value="noche">Noche</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Capacidad máxima</label>
                <input type="number" name="capacidad" id="sec-capacidad" class="form-control" min="1" value="30">
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
                <button type="button" onclick="document.getElementById('modal-sec').style.display='none'" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const secRouteBase = "{{ url('secciones') }}";
function editarSeccion(id, nombre, turno, capacidad) {
    document.getElementById('modal-sec-title').textContent = 'Editar Sección';
    document.getElementById('form-sec').action = secRouteBase + '/' + id;
    document.getElementById('sec-method').value   = 'PUT';
    document.getElementById('sec-nombre').value   = nombre;
    document.getElementById('sec-turno').value    = turno;
    document.getElementById('sec-capacidad').value= capacidad;
    document.getElementById('modal-sec').style.display = 'flex';
}
</script>
@endpush
