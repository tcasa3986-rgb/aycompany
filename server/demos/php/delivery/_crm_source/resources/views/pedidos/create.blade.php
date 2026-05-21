@extends('layouts.app')
@section('title', 'Nuevo Pedido')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
        <li class="breadcrumb-item active">Nuevo Pedido</li>
    </ol></nav>
@endsection

@push('styles')
<style>
.producto-item { cursor: pointer; transition: all 0.2s; }
.producto-item:hover { background: #f0f5ff; border-color: #0d6efd !important; }
.item-row { animation: fadeIn 0.3s; }
@keyframes fadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Nuevo Pedido</h4>
    <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<form method="POST" action="{{ route('pedidos.store') }}" id="pedidoForm">
    @csrf
    <div class="row g-3">
        <!-- Columna izquierda -->
        <div class="col-lg-8">
            <!-- Cliente -->
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Datos del Cliente</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Cliente *</label>
                            <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" id="selectCliente" required>
                                <option value="">Seleccionar cliente...</option>
                                @foreach($clientes as $c)
                                <option value="{{ $c->id }}"
                                    data-dir="{{ $c->direccion }}"
                                    data-dist="{{ $c->distrito }}"
                                    data-tel="{{ $c->telefono }}"
                                    {{ request('cliente_id')==$c->id || old('cliente_id')==$c->id?'selected':'' }}>
                                    {{ $c->nombre_completo }} — {{ $c->telefono }}
                                </option>
                                @endforeach
                            </select>
                            @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Repartidor (opcional)</label>
                            <select name="repartidor_id" class="form-select">
                                <option value="">Sin asignar</option>
                                @foreach($repartidores as $rep)
                                <option value="{{ $rep->id }}">{{ $rep->nombre }} ({{ $rep->zona_asignada }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dirección de Entrega *</label>
                            <input type="text" name="direccion_entrega" id="dirEntrega" value="{{ old('direccion_entrega') }}"
                                class="form-control @error('direccion_entrega') is-invalid @enderror" required>
                            @error('direccion_entrega')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Referencia</label>
                            <input type="text" name="referencia_entrega" value="{{ old('referencia_entrega') }}" class="form-control" placeholder="Frente al parque...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Distrito</label>
                            <input type="text" name="distrito_entrega" id="distEntrega" value="{{ old('distrito_entrega') }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-grid me-2 text-primary"></i>Agregar Productos</div>
                <div class="card-body">
                    <!-- Búsqueda de productos -->
                    <input type="text" id="buscarProducto" class="form-control mb-3" placeholder="🔍 Buscar producto por nombre o código...">
                    <div class="row g-2 mb-3" id="listaProductos">
                        @foreach($productos as $p)
                        <div class="col-md-6 producto-card" data-nombre="{{ strtolower($p->nombre) }}" data-codigo="{{ strtolower($p->codigo) }}">
                            <div class="producto-item border rounded-3 p-2" onclick="agregarProducto({{ $p->id }}, '{{ addslashes($p->nombre) }}', {{ $p->precio_final }})">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold small">{{ $p->nombre }}</span>
                                    <span class="text-primary fw-bold">S/ {{ number_format($p->precio_final, 2) }}</span>
                                </div>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    <code>{{ $p->codigo }}</code> · {{ $p->categoria?->nombre }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Items del pedido -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span><i class="bi bi-list-check me-2 text-primary"></i>Items del Pedido</span>
                    <span class="badge bg-primary" id="contadorItems">0 items</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr>
                                <th class="ps-3">Producto</th>
                                <th>Precio Unit.</th>
                                <th width="100">Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr></thead>
                            <tbody id="itemsContainer">
                                <tr id="emptyRow"><td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-cart-x d-block fs-3 mb-2"></i>Agrega productos al pedido
                                </td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna derecha -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-receipt me-2 text-primary"></i>Resumen del Pedido</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <strong id="resumenSubtotal">S/ 0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Delivery:</span>
                        <div class="d-flex align-items-center gap-2">
                            <span>S/</span>
                            <input type="number" step="0.50" name="costo_delivery" id="costoDelivery" value="{{ old('costo_delivery', 5) }}"
                                class="form-control form-control-sm text-end" style="width:80px" min="0">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Descuento:</span>
                        <div class="d-flex align-items-center gap-2">
                            <span>S/</span>
                            <input type="number" step="0.50" name="descuento" id="descuento" value="{{ old('descuento', 0) }}"
                                class="form-control form-control-sm text-end" style="width:80px" min="0">
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold fs-5">TOTAL:</span>
                        <strong class="fs-5 text-primary" id="resumenTotal">S/ 0.00</strong>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-credit-card me-2 text-primary"></i>Pago y Entrega</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Método de Pago *</label>
                        <select name="tipo_pago" class="form-select" required>
                            @foreach(['efectivo'=>'💵 Efectivo','tarjeta'=>'💳 Tarjeta','transferencia'=>'🏦 Transferencia','yape'=>'📱 Yape','plin'=>'📱 Plin'] as $val=>$lbl)
                            <option value="{{ $val }}" {{ old('tipo_pago')===$val?'selected':'' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha Programada (opcional)</label>
                        <input type="datetime-local" name="fecha_programada" value="{{ old('fecha_programada') }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Notas del Pedido</label>
                        <textarea name="notas" rows="3" class="form-control" placeholder="Instrucciones especiales...">{{ old('notas') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-check-circle me-2"></i>Crear Pedido
                    </button>
                    <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let items = {};
let itemIndex = 0;

// Auto-completar dirección al seleccionar cliente
document.getElementById('selectCliente').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if(opt.value) {
        document.getElementById('dirEntrega').value  = opt.dataset.dir || '';
        document.getElementById('distEntrega').value = opt.dataset.dist || '';
    }
});

// Buscar producto
document.getElementById('buscarProducto').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.producto-card').forEach(card => {
        const match = card.dataset.nombre.includes(q) || card.dataset.codigo.includes(q);
        card.style.display = match || !q ? '' : 'none';
    });
});

function agregarProducto(id, nombre, precio) {
    if(items[id]) {
        items[id].cantidad++;
        document.getElementById('qty_' + id).value = items[id].cantidad;
    } else {
        items[id] = { nombre, precio, cantidad: 1 };
        itemIndex++;
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.id = 'row_' + id;
        tr.innerHTML = `
            <td class="ps-3">
                <div class="fw-semibold small">${nombre}</div>
                <input type="hidden" name="items[${id}][producto_id]" value="${id}">
                <input type="hidden" name="items[${id}][precio]" value="${precio}">
                <input type="hidden" name="items[${id}][notas]" value="">
            </td>
            <td>S/ ${precio.toFixed(2)}</td>
            <td>
                <input type="number" name="items[${id}][cantidad]" id="qty_${id}" value="1" min="1"
                    class="form-control form-control-sm" style="width:65px"
                    onchange="cambiarCantidad(${id}, this.value)">
            </td>
            <td id="sub_${id}">S/ ${precio.toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="quitarItem(${id})">
                    <i class="bi bi-x"></i>
                </button>
            </td>
        `;
        document.getElementById('emptyRow').style.display = 'none';
        document.getElementById('itemsContainer').appendChild(tr);
    }
    actualizarTotales();
}

function cambiarCantidad(id, qty) {
    items[id].cantidad = parseInt(qty) || 1;
    const sub = items[id].precio * items[id].cantidad;
    document.getElementById('sub_' + id).textContent = 'S/ ' + sub.toFixed(2);
    actualizarTotales();
}

function quitarItem(id) {
    delete items[id];
    document.getElementById('row_' + id).remove();
    if(Object.keys(items).length === 0) {
        document.getElementById('emptyRow').style.display = '';
    }
    actualizarTotales();
}

function actualizarTotales() {
    let subtotal = 0;
    for(const id in items) subtotal += items[id].precio * items[id].cantidad;
    const delivery  = parseFloat(document.getElementById('costoDelivery').value) || 0;
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    const total = subtotal + delivery - descuento;
    const count = Object.keys(items).length;

    document.getElementById('resumenSubtotal').textContent = 'S/ ' + subtotal.toFixed(2);
    document.getElementById('resumenTotal').textContent    = 'S/ ' + total.toFixed(2);
    document.getElementById('contadorItems').textContent   = count + ' item' + (count !== 1 ? 's' : '');
}

document.getElementById('costoDelivery').addEventListener('input', actualizarTotales);
document.getElementById('descuento').addEventListener('input', actualizarTotales);
</script>
@endpush
