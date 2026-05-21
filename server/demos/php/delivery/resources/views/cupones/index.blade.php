@extends('layouts.app')
@section('title', 'Cupones y Promociones')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Cupones</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-ticket-perforated me-2 text-success"></i>Cupones y Promociones</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mNuevo"><i class="bi bi-plus-lg me-1"></i>Nuevo cupón</button>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Código</th>
                    <th>Descripción</th>
                    <th>Tipo / Valor</th>
                    <th>Mín. pedido</th>
                    <th>Vigencia</th>
                    <th class="text-center">Usos</th>
                    <th>Estado</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cupones as $c)
                <tr>
                    <td class="ps-3"><code class="fs-6">{{ $c->codigo }}</code>
                        @if($c->solo_primer_pedido)<span class="badge bg-info ms-1" title="Solo primer pedido">1°</span>@endif
                    </td>
                    <td>{{ $c->descripcion }}</td>
                    <td>
                        @if($c->tipo === 'porcentaje')
                            <span class="badge bg-warning">{{ rtrim(rtrim($c->valor,'0'),'.') }}% OFF</span>
                            @if($c->descuento_maximo)<small class="text-muted d-block">Máx S/ {{ $c->descuento_maximo }}</small>@endif
                        @else
                            <span class="badge bg-success">S/ {{ $c->valor }}</span>
                        @endif
                    </td>
                    <td>S/ {{ number_format($c->monto_minimo, 2) }}</td>
                    <td><small>
                        {{ $c->valido_desde ? $c->valido_desde->format('d/m') : '—' }}
                         a
                        {{ $c->valido_hasta ? $c->valido_hasta->format('d/m/Y') : 'sin límite' }}
                    </small></td>
                    <td class="text-center">{{ $c->usos_actuales }} / {{ $c->usos_maximos ?? '∞' }}</td>
                    <td><span class="badge bg-{{ $c->activo ? 'success' : 'secondary' }}">{{ $c->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-warning" onclick='editarCupon(@json($c))'><i class="bi bi-pencil"></i></button>
                        <form method="POST" action="{{ route('cupones.destroy', $c) }}" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger ms-1"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No hay cupones todavía</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $cupones->links() }}</div>
</div>

<!-- Modal nuevo / edición -->
<div class="modal fade" id="mNuevo" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <form method="POST" action="{{ route('cupones.store') }}" id="cForm">
            @csrf
            <input type="hidden" name="_method" id="cMethod" value="POST">
            <div class="modal-header"><h5 class="modal-title" id="cTitulo">Nuevo cupón</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Código *</label>
                        <input type="text" name="codigo" id="cCodigo" class="form-control text-uppercase" required maxlength="30" placeholder="VERANO20">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Descripción</label>
                        <input type="text" name="descripcion" id="cDesc" class="form-control" placeholder="Promo de verano">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tipo *</label>
                        <select name="tipo" id="cTipo" class="form-select">
                            <option value="porcentaje">Porcentaje (%)</option>
                            <option value="monto">Monto fijo (S/)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Valor *</label>
                        <input type="number" step="0.01" name="valor" id="cValor" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Descuento máximo</label>
                        <input type="number" step="0.01" name="descuento_maximo" id="cMax" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Monto mínimo de pedido</label>
                        <input type="number" step="0.01" name="monto_minimo" id="cMin" class="form-control" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Usos máximos</label>
                        <input type="number" name="usos_maximos" id="cUsosMax" class="form-control" placeholder="Ilimitado">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="solo_primer_pedido" value="1" id="cPrimero">
                            <label class="form-check-label" for="cPrimero">Solo primer pedido</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Válido desde</label>
                        <input type="date" name="valido_desde" id="cDesde" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Válido hasta</label>
                        <input type="date" name="valido_hasta" id="cHasta" class="form-control">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="cActivo" checked>
                            <label class="form-check-label" for="cActivo">Cupón activo</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            </div>
        </form>
    </div></div>
</div>

@push('scripts')
<script>
function editarCupon(c) {
    const f = document.getElementById('cForm');
    f.action = '/cupones/' + c.id;
    document.getElementById('cMethod').value = 'PUT';
    document.getElementById('cTitulo').textContent = 'Editar cupón';
    document.getElementById('cCodigo').value = c.codigo;
    document.getElementById('cDesc').value   = c.descripcion || '';
    document.getElementById('cTipo').value   = c.tipo;
    document.getElementById('cValor').value  = c.valor;
    document.getElementById('cMax').value    = c.descuento_maximo || '';
    document.getElementById('cMin').value    = c.monto_minimo;
    document.getElementById('cUsosMax').value= c.usos_maximos || '';
    document.getElementById('cPrimero').checked = c.solo_primer_pedido == 1;
    document.getElementById('cActivo').checked  = c.activo == 1;
    document.getElementById('cDesde').value = c.valido_desde ? c.valido_desde.substring(0,10) : '';
    document.getElementById('cHasta').value = c.valido_hasta ? c.valido_hasta.substring(0,10) : '';
    new bootstrap.Modal(document.getElementById('mNuevo')).show();
}
</script>
@endpush
@endsection
