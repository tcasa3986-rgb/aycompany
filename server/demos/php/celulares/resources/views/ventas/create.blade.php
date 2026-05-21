@extends('layouts.app')
@section('title', 'Nueva Venta')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" style="color:#a855f7;">Ventas</a></li>
    <li class="breadcrumb-item active">Nueva Venta</li>
@endsection

@section('content')
<div class="row g-4">

    {{-- ── Formulario principal ──────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Registrar Venta</h5>
                <p class="text-muted mb-4" style="font-size:13px;">Selecciona los productos y completa los datos</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e)
                            <div style="font-size:13px;"><i class="fas fa-exclamation-circle me-1"></i>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                <form id="formVenta" action="{{ route('ventas.store') }}" method="POST">
                    @csrf

                    {{-- Cliente --}}
                    <div class="mb-4">
                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" id="clienteSelect" class="form-select" required>
                            <option value="">— Seleccionar cliente —</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}" {{ old('cliente_id')==$c->id?'selected':'' }}>
                                    {{ $c->nombre_completo }} — {{ $c->telefono }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Buscador de productos --}}
                    <div class="mb-3">
                        <label class="form-label">Agregar Producto</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                            <input type="text" id="buscadorProducto" class="form-control"
                                   placeholder="Busca por nombre o código...">
                            <select id="selectProducto" class="form-select" style="max-width:220px;">
                                <option value="">— Seleccionar —</option>
                                @foreach($productos as $p)
                                    <option value="{{ $p->id }}"
                                            data-nombre="{{ $p->nombre }}"
                                            data-precio="{{ $p->precio_venta }}"
                                            data-stock="{{ $p->stock }}"
                                            data-codigo="{{ $p->codigo }}">
                                        {{ $p->nombre }} ({{ $p->stock }} disp.)
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary" onclick="agregarProducto()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Tabla de productos seleccionados --}}
                    <div class="table-responsive mb-3">
                        <table class="table align-middle mb-0" id="tablaProductos">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th style="width:80px;">Cant.</th>
                                    <th style="width:110px;">Precio Unit.</th>
                                    <th style="width:100px;">Descuento</th>
                                    <th style="width:110px;">Subtotal</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="productosBody">
                                <tr id="filaVacia">
                                    <td colspan="6" class="text-center text-muted py-4" style="font-size:13px;">
                                        <i class="fas fa-shopping-basket fa-2x mb-2 d-block opacity-40"></i>
                                        Agrega productos a la venta
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Método de pago y notas --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                            <select name="metodo_pago" class="form-select" required>
                                <option value="efectivo">💵 Efectivo</option>
                                <option value="tarjeta">💳 Tarjeta</option>
                                <option value="yape">📱 Yape</option>
                                <option value="plin">📲 Plin</option>
                                <option value="transferencia">🏦 Transferencia</option>
                                <option value="cuotas">📅 Cuotas</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descuento General (S/)</label>
                            <input type="number" class="form-control" name="descuento_general"
                                   id="descuentoGeneral" min="0" step="0.01" value="0"
                                   oninput="calcularTotales()">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" name="notas" rows="2"
                                      placeholder="Observaciones de la venta..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Panel de resumen ──────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card" style="position:sticky; top:90px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4">Resumen de Venta</h6>

                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span class="text-muted">Subtotal</span>
                    <span id="resSubtotal" class="fw-500">S/ 0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span class="text-muted">Descuento</span>
                    <span id="resDescuento" class="text-danger fw-500">— S/ 0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span class="text-muted">IGV (18%)</span>
                    <span id="resImpuesto" class="fw-500">S/ 0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold" style="font-size:16px;">Total</span>
                    <span id="resTotal" style="font-size:22px; font-weight:700; color:#a855f7;">S/ 0.00</span>
                </div>

                <div class="mb-3 p-3 rounded-3" style="background:#f8f5ff; font-size:13px;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Productos</span>
                        <span id="resCantProductos" class="fw-500">0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Unidades</span>
                        <span id="resUnidades" class="fw-500">0</span>
                    </div>
                </div>

                <button type="submit" form="formVenta" class="btn btn-primary w-100 py-2" id="btnVenta" disabled>
                    <i class="fas fa-cash-register me-2"></i>Registrar Venta
                </button>

                <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary w-100 mt-2 py-2">
                    Cancelar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let productosSeleccionados = {};
let contador = 0;

function agregarProducto() {
    const select = document.getElementById('selectProducto');
    const opt = select.options[select.selectedIndex];
    if (!opt.value) { alert('Selecciona un producto'); return; }

    const id     = opt.value;
    const nombre = opt.dataset.nombre;
    const precio = parseFloat(opt.dataset.precio);
    const stock  = parseInt(opt.dataset.stock);

    if (productosSeleccionados[id]) {
        // Ya existe: incrementar cantidad
        const fila = document.getElementById('fila-' + id);
        const cantInput = fila.querySelector('.cant-input');
        const nuevaCant = parseInt(cantInput.value) + 1;
        if (nuevaCant > stock) { alert('Stock insuficiente'); return; }
        cantInput.value = nuevaCant;
        calcularFila(id);
    } else {
        productosSeleccionados[id] = { nombre, precio, stock };
        document.getElementById('filaVacia').style.display = 'none';

        const tbody = document.getElementById('productosBody');
        const tr = document.createElement('tr');
        tr.id = 'fila-' + id;
        tr.innerHTML = `
            <td>
                <input type="hidden" name="productos[${id}][id]" value="${id}">
                <div style="font-size:13.5px; font-weight:500;">${nombre}</div>
                <div style="font-size:11px; color:#9ca3af;">Stock: ${stock}</div>
            </td>
            <td>
                <input type="number" name="productos[${id}][cantidad]" value="1" min="1" max="${stock}"
                       class="form-control form-control-sm cant-input" style="width:65px;"
                       oninput="calcularFila('${id}')">
            </td>
            <td style="font-size:13.5px; font-weight:500;">S/ ${precio.toFixed(2)}</td>
            <td>
                <input type="number" name="productos[${id}][descuento]" value="0" min="0" step="0.01"
                       class="form-control form-control-sm desc-input" style="width:80px;"
                       oninput="calcularFila('${id}')">
            </td>
            <td id="sub-${id}" style="font-size:13.5px; font-weight:600; color:#1e1b4b;">
                S/ ${precio.toFixed(2)}
            </td>
            <td>
                <button type="button" class="btn btn-sm"
                        style="background:#fee2e2; color:#dc2626; border-radius:8px; padding:4px 8px;"
                        onclick="quitarProducto('${id}')">
                    <i class="fas fa-times fa-xs"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    select.selectedIndex = 0;
    calcularTotales();
}

function calcularFila(id) {
    const fila  = document.getElementById('fila-' + id);
    const cant  = parseFloat(fila.querySelector('.cant-input').value) || 0;
    const desc  = parseFloat(fila.querySelector('.desc-input').value) || 0;
    const sub   = (productosSeleccionados[id].precio * cant) - desc;
    document.getElementById('sub-' + id).textContent = 'S/ ' + Math.max(sub, 0).toFixed(2);
    calcularTotales();
}

function quitarProducto(id) {
    document.getElementById('fila-' + id).remove();
    delete productosSeleccionados[id];
    if (Object.keys(productosSeleccionados).length === 0) {
        document.getElementById('filaVacia').style.display = '';
    }
    calcularTotales();
}

function calcularTotales() {
    let subtotal    = 0;
    let unidades    = 0;
    const productos = Object.keys(productosSeleccionados);

    productos.forEach(id => {
        const fila = document.getElementById('fila-' + id);
        if (!fila) return;
        const cant = parseFloat(fila.querySelector('.cant-input').value) || 0;
        const desc = parseFloat(fila.querySelector('.desc-input').value) || 0;
        subtotal  += (productosSeleccionados[id].precio * cant) - desc;
        unidades  += cant;
    });

    const descGen   = parseFloat(document.getElementById('descuentoGeneral').value) || 0;
    const base      = Math.max(subtotal - descGen, 0);
    const impuesto  = base * 0.18;
    const total     = base + impuesto;

    document.getElementById('resSubtotal').textContent    = 'S/ ' + subtotal.toFixed(2);
    document.getElementById('resDescuento').textContent   = '— S/ ' + descGen.toFixed(2);
    document.getElementById('resImpuesto').textContent    = 'S/ ' + impuesto.toFixed(2);
    document.getElementById('resTotal').textContent       = 'S/ ' + total.toFixed(2);
    document.getElementById('resCantProductos').textContent = productos.length;
    document.getElementById('resUnidades').textContent    = unidades;

    document.getElementById('btnVenta').disabled =
        (productos.length === 0 || !document.getElementById('clienteSelect').value);
}

document.getElementById('clienteSelect').addEventListener('change', calcularTotales);

// Filtro en el buscador de productos
document.getElementById('buscadorProducto').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const opts = document.getElementById('selectProducto').options;
    for (let i = 1; i < opts.length; i++) {
        opts[i].hidden = !opts[i].text.toLowerCase().includes(q);
    }
});
</script>
@endpush
