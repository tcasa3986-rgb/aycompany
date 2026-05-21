@extends('layouts.app')
@section('title', 'Zonas de Delivery')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Zonas</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-geo-alt me-2 text-primary"></i>Zonas de Delivery y Tarifas</h4>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Distrito / Zona</th>
                            <th>Costo</th>
                            <th>Tiempo</th>
                            <th>Mínimo</th>
                            <th>Estado</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($zonas as $z)
                        <tr>
                            <td class="ps-3">
                                <strong>{{ $z->nombre }}</strong>
                                @if($z->distrito)<div class="text-muted small">{{ $z->distrito }}</div>@endif
                            </td>
                            <td><span class="badge bg-success">S/ {{ number_format($z->costo_delivery, 2) }}</span></td>
                            <td>{{ $z->tiempo_estimado_min }} min</td>
                            <td>S/ {{ number_format($z->monto_minimo_pedido, 2) }}</td>
                            <td><span class="badge bg-{{ $z->activo ? 'success' : 'secondary' }}">{{ $z->activo ? 'Activa' : 'Inactiva' }}</span></td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-warning" onclick='editarZona(@json($z))'><i class="bi bi-pencil"></i></button>
                                <form method="POST" action="{{ route('zonas.destroy', $z) }}" class="d-inline" onsubmit="return confirm('¿Eliminar zona?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger ms-1"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Aún no has creado zonas. Empieza por agregar las áreas de tu cobertura.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header" id="formHeader"><i class="bi bi-plus-circle me-2 text-primary"></i>Nueva Zona</div>
            <div class="card-body">
                <form method="POST" action="{{ route('zonas.store') }}" id="zonaForm">
                    @csrf
                    <input type="hidden" name="_method" id="zMethod" value="POST">
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Nombre de la zona *</label>
                        <input type="text" name="nombre" id="zNombre" class="form-control" placeholder="Ej: Centro - San Isidro" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Distrito</label>
                        <input type="text" name="distrito" id="zDistrito" class="form-control" placeholder="San Isidro">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Costo S/ *</label>
                            <input type="number" step="0.50" name="costo_delivery" id="zCosto" class="form-control" required value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tiempo (min) *</label>
                            <input type="number" name="tiempo_estimado_min" id="zTiempo" class="form-control" required value="30">
                        </div>
                    </div>
                    <div class="mb-2 mt-2">
                        <label class="form-label fw-semibold small">Monto mínimo de pedido</label>
                        <input type="number" step="0.50" name="monto_minimo_pedido" id="zMinimo" class="form-control" value="0">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Descripción / referencia</label>
                        <textarea name="descripcion" id="zDesc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="zActivo" checked>
                        <label class="form-check-label" for="zActivo">Activa</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="zBtnGuardar"><i class="bi bi-plus-lg me-1"></i>Crear Zona</button>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2 d-none" id="zBtnCancelar" onclick="cancelarZona()">Cancelar edición</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editarZona(z) {
    const f = document.getElementById('zonaForm');
    f.action = '/zonas/' + z.id;
    document.getElementById('zMethod').value = 'PUT';
    document.getElementById('zNombre').value   = z.nombre;
    document.getElementById('zDistrito').value = z.distrito || '';
    document.getElementById('zCosto').value    = z.costo_delivery;
    document.getElementById('zTiempo').value   = z.tiempo_estimado_min;
    document.getElementById('zMinimo').value   = z.monto_minimo_pedido;
    document.getElementById('zDesc').value     = z.descripcion || '';
    document.getElementById('zActivo').checked = z.activo == 1;
    document.getElementById('formHeader').innerHTML = '<i class="bi bi-pencil me-2 text-warning"></i>Editar Zona';
    document.getElementById('zBtnGuardar').innerHTML = '<i class="bi bi-check-lg me-1"></i>Actualizar';
    document.getElementById('zBtnGuardar').className = 'btn btn-warning w-100';
    document.getElementById('zBtnCancelar').classList.remove('d-none');
    f.scrollIntoView({behavior:'smooth'});
}
function cancelarZona() {
    const f = document.getElementById('zonaForm');
    f.reset();
    f.action = '{{ route("zonas.store") }}';
    document.getElementById('zMethod').value = 'POST';
    document.getElementById('formHeader').innerHTML = '<i class="bi bi-plus-circle me-2 text-primary"></i>Nueva Zona';
    document.getElementById('zBtnGuardar').innerHTML = '<i class="bi bi-plus-lg me-1"></i>Crear Zona';
    document.getElementById('zBtnGuardar').className = 'btn btn-primary w-100';
    document.getElementById('zBtnCancelar').classList.add('d-none');
}
</script>
@endpush
@endsection
