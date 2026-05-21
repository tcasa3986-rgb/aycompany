@extends('layouts.app')
@section('title', 'Categorías')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Categorías</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-tags me-2 text-primary"></i>Categorías de Productos</h4>
</div>

<div class="row g-3">
    <!-- Lista de categorías -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Orden</th>
                                <th>Categoría</th>
                                <th>Productos</th>
                                <th>Estado</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($categorias as $cat)
                            <tr>
                                <td class="ps-3 text-muted">{{ $cat->orden }}</td>
                                <td>
                                    <span class="badge rounded-pill me-2" style="background:{{ $cat->color }}25;color:{{ $cat->color }};border:1px solid {{ $cat->color }}">
                                        <i class="{{ $cat->icono }} me-1"></i>{{ $cat->nombre }}
                                    </span>
                                    @if($cat->descripcion)<div class="text-muted small mt-1">{{ $cat->descripcion }}</div>@endif
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $cat->productos_count }}</span></td>
                                <td><span class="badge bg-{{ $cat->activo ? 'success' : 'secondary' }}">{{ $cat->activo ? 'Activa' : 'Inactiva' }}</span></td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-warning" onclick="editarCategoria({{ $cat->toJson() }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($cat->productos_count === 0)
                                    <form method="POST" action="{{ route('categorias.destroy', $cat) }}" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger ms-1"><i class="bi bi-trash"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No hay categorías</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario nueva categoría -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header" id="formHeader"><i class="bi bi-plus-circle me-2 text-primary"></i>Nueva Categoría</div>
            <div class="card-body">
                <form method="POST" action="{{ route('categorias.store') }}" id="catForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="cat_id" id="catId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre *</label>
                        <input type="text" name="nombre" id="catNombre" class="form-control" required placeholder="Ej: Pizzas">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <input type="text" name="descripcion" id="catDesc" class="form-control" placeholder="Descripción breve">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label fw-semibold">Icono Bootstrap</label>
                            <input type="text" name="icono" id="catIcono" class="form-control" placeholder="bi-tag" value="bi-tag">
                            <div class="form-text"><a href="https://icons.getbootstrap.com" target="_blank">Ver íconos</a></div>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Color</label>
                            <input type="color" name="color" id="catColor" class="form-control form-control-color w-100" value="#0d6efd">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" name="orden" id="catOrden" class="form-control" value="{{ $categorias->count() + 1 }}" min="0">
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" value="1" id="catActivo" checked>
                                <label class="form-check-label" for="catActivo">Activa</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="btnGuardar">
                        <i class="bi bi-plus-lg me-1"></i>Crear Categoría
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2 d-none" id="btnCancelar" onclick="cancelarEdicion()">
                        Cancelar edición
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editarCategoria(cat) {
    document.getElementById('formHeader').innerHTML = '<i class="bi bi-pencil me-2 text-warning"></i>Editar Categoría';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('catForm').action = '/categorias/' + cat.id;
    document.getElementById('catId').value = cat.id;
    document.getElementById('catNombre').value = cat.nombre;
    document.getElementById('catDesc').value = cat.descripcion || '';
    document.getElementById('catIcono').value = cat.icono || 'bi-tag';
    document.getElementById('catColor').value = cat.color || '#0d6efd';
    document.getElementById('catOrden').value = cat.orden;
    document.getElementById('catActivo').checked = cat.activo == 1;
    document.getElementById('btnGuardar').innerHTML = '<i class="bi bi-check-lg me-1"></i>Actualizar';
    document.getElementById('btnGuardar').className = 'btn btn-warning w-100';
    document.getElementById('btnCancelar').classList.remove('d-none');
    document.getElementById('catForm').scrollIntoView({behavior:'smooth'});
}
function cancelarEdicion() {
    document.getElementById('catForm').reset();
    document.getElementById('formHeader').innerHTML = '<i class="bi bi-plus-circle me-2 text-primary"></i>Nueva Categoría';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('catForm').action = '{{ route("categorias.store") }}';
    document.getElementById('btnGuardar').innerHTML = '<i class="bi bi-plus-lg me-1"></i>Crear Categoría';
    document.getElementById('btnGuardar').className = 'btn btn-primary w-100';
    document.getElementById('btnCancelar').classList.add('d-none');
}
</script>
@endpush
