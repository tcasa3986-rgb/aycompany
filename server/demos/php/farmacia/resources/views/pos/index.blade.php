@extends('layouts.app')
@section('title', 'POS')
@section('section', 'Punto de Venta')

@section('content')
@if (! $cajaAbierta)
    <div class="card card-pad mb-5 bg-amber-50 border-amber-200">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-amber-800 font-semibold">⚠ No tienes una caja abierta</p>
                <p class="text-amber-700 text-sm">Para poder cobrar ventas necesitas abrir tu caja primero.</p>
            </div>
            <a href="{{ route('cajas.index') }}" class="btn-primary">Abrir caja</a>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5" id="posApp">

    {{-- Buscador y resultados --}}
    <div class="card card-pad lg:col-span-2">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">Buscar producto</h2>
        <div class="relative">
            <input type="text" id="posSearch" placeholder="Nombre, código o principio activo..." class="input pl-9">
            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400">
                <x-icon name="search" class="h-4 w-4" />
            </span>
        </div>

        <div class="mt-3 max-h-72 overflow-y-auto border border-gray-100 rounded-lg" id="posResults">
            <p class="p-4 text-sm text-gray-400">Empieza a escribir para buscar productos…</p>
        </div>

        <div id="interactionAlerts" class="mt-4 hidden">
            <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                <div class="flex items-center gap-2 text-rose-700 font-bold mb-2">
                    <x-icon name="box" class="h-5 w-5" />
                    <span>¡Alerta de Interacción Medicamentosa!</span>
                </div>
                <ul id="interactionList" class="text-sm text-rose-600 list-disc list-inside space-y-1"></ul>
            </div>
        </div>

        <h3 class="mt-6 mb-2 text-sm font-semibold text-gray-700 uppercase tracking-wider">Carrito</h3>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-right">Precio</th>
                        <th class="w-28">Cant.</th>
                        <th class="text-right">Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cartBody" class="divide-y divide-gray-100">
                    <tr><td colspan="5" class="py-6 text-center text-gray-400">Carrito vacío</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Resumen y cobro --}}
    <div class="card card-pad">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">Cobro</h2>

        <div class="space-y-3">
            <div>
                <label class="label">Cliente</label>
                <select id="cliente" class="input">
                    <option value="" data-puntos="0">— Cliente genérico —</option>
                    @foreach ($clientes as $c)
                        <option value="{{ $c->id }}" data-puntos="{{ $c->puntos_fidelidad }}">
                            {{ trim($c->nombres . ' ' . $c->apellidos) }} @if($c->documento) · {{ $c->documento }} @endif ({{ $c->puntos_fidelidad }} pts)
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="puntosBox" class="hidden bg-farmacia-50 p-2 rounded-lg border border-farmacia-100 mb-3">
                <p class="text-xs text-farmacia-700 font-semibold mb-1">Fidelidad: <span id="puntosDisponibles">0</span> pts disponibles</p>
                <div class="flex items-center gap-2">
                    <input type="number" id="puntosCanje" min="0" value="0" class="input py-1 text-sm" placeholder="Pts a canjear">
                    <span class="text-xs text-gray-500">1 pt = S/ 1</span>
                </div>
            </div>

            <div>
                <label class="label">Forma de pago</label>
                <select id="formaPago" class="input">
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="mixto">Mixto</option>
                    <option value="credito">Crédito</option>
                </select>
            </div>

            <div>
                <label class="label">Descuento (S/)</label>
                <input type="number" id="descuento" min="0" step="0.01" value="0" class="input">
            </div>

            <div>
                <label class="label flex justify-between">
                    <span>Pago recibido (S/)</span>
                    <span class="text-[10px] text-gray-400 uppercase">F9 para enfocar</span>
                </label>
                <input type="number" id="pago" min="0" step="0.01" value="0" class="input text-lg font-bold text-gray-700 focus:ring-farmacia-500">
                <div class="grid grid-cols-4 gap-1 mt-2">
                    <button type="button" onclick="$('pago').value=10; recomputeTotals();" class="text-[10px] bg-gray-100 hover:bg-gray-200 py-1 rounded">10</button>
                    <button type="button" onclick="$('pago').value=20; recomputeTotals();" class="text-[10px] bg-gray-100 hover:bg-gray-200 py-1 rounded">20</button>
                    <button type="button" onclick="$('pago').value=50; recomputeTotals();" class="text-[10px] bg-gray-100 hover:bg-gray-200 py-1 rounded">50</button>
                    <button type="button" onclick="$('pago').value=100; recomputeTotals();" class="text-[10px] bg-gray-100 hover:bg-gray-200 py-1 rounded">100</button>
                </div>
            </div>

            <div>
                <label class="label">Observaciones</label>
                <textarea id="obs" rows="2" class="input"></textarea>
            </div>
        </div>

        <div class="mt-5 border-t border-gray-100 pt-4 space-y-1 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Subtotal</span> <span id="sumSub">S/ 0.00</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Desc. Manual</span> <span id="sumDesc">S/ 0.00</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Desc. Puntos</span> <span id="sumPts">S/ 0.00</span></div>
            <div class="flex justify-between"><span class="text-gray-500">IGV (18%)</span> <span id="sumImp">S/ 0.00</span></div>
            <div class="flex justify-between text-lg font-bold text-farmacia-700 mt-2">
                <span>TOTAL</span> <span id="sumTot">S/ 0.00</span>
            </div>
            <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg mt-2">
                <span class="text-xs font-bold text-gray-500 uppercase">Su cambio:</span>
                <span id="sumCam" class="text-xl font-black text-emerald-600">S/ 0.00</span>
            </div>
        </div>

        <button type="button" id="btnPagar" class="btn-primary w-full mt-5">Cobrar y emitir</button>
        <p id="msg" class="text-xs mt-2"></p>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const cart = new Map(); // producto_id -> {producto, cant}
    const fmt = n => 'S/ ' + Number(n).toFixed(2);

    const $ = id => document.getElementById(id);
    const search = $('posSearch'), results = $('posResults'), body = $('cartBody');

    let timer = null;
    search.addEventListener('input', () => {
        clearTimeout(timer);
        const q = search.value.trim();
        if (q.length < 2) {
            results.innerHTML = '<p class="p-4 text-sm text-gray-400">Empieza a escribir para buscar productos…</p>';
            return;
        }
        timer = setTimeout(() => fetchProducts(q), 200);
    });

    function fetchProducts(q) {
        fetch(`{{ route('pos.buscar') }}?q=` + encodeURIComponent(q))
            .then(r => r.json())
            .then(rows => {
                if (!rows.length) {
                    results.innerHTML = '<p class="p-4 text-sm text-gray-400">Sin resultados.</p>';
                    return;
                }
                results.innerHTML = rows.map(p => `
                    <button type="button" data-id="${p.id}"
                        data-nombre="${encodeURIComponent(p.nombre)}"
                        data-principio="${encodeURIComponent(p.principio_activo || '')}"
                        data-precio="${p.precio_venta}"
                        data-stock="${p.stock}"
                        class="w-full flex items-center justify-between gap-3 px-4 py-2 hover:bg-farmacia-50 border-b border-gray-50 text-left">
                        <div>
                            <div class="text-sm font-medium text-gray-800">${p.nombre}</div>
                            <div class="text-xs text-gray-500">${p.codigo} · stock: ${p.stock}</div>
                        </div>
                        <div class="text-sm font-semibold text-farmacia-700">${fmt(p.precio_venta)}</div>
                    </button>
                `).join('');
                results.querySelectorAll('button').forEach(b => b.addEventListener('click', () => addToCart({
                    id: b.dataset.id,
                    nombre: decodeURIComponent(b.dataset.nombre),
                    principio: decodeURIComponent(b.dataset.principio),
                    precio: Number(b.dataset.precio),
                    stock: Number(b.dataset.stock),
                })));
            });
    }

    function addToCart(p) {
        const ex = cart.get(p.id);
        if (ex) {
            if (ex.cant + 1 > p.stock) return setMsg('Stock insuficiente', 'rose');
            ex.cant++;
        } else {
            cart.set(p.id, { ...p, cant: 1 });
        }
        renderCart();
    }

    function renderCart() {
        if (cart.size === 0) {
            body.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-gray-400">Carrito vacío</td></tr>';
            recomputeTotals();
            return;
        }
        body.innerHTML = '';
        cart.forEach((item, id) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="font-medium text-gray-800">${item.nombre}</td>
                <td class="text-right">${fmt(item.precio)}</td>
                <td>
                    <input type="number" min="1" max="${item.stock}" value="${item.cant}"
                           class="input w-24 text-right" data-id="${id}">
                </td>
                <td class="text-right font-semibold">${fmt(item.precio * item.cant)}</td>
                <td class="text-right">
                    <button type="button" class="text-rose-500 hover:underline text-xs" data-rm="${id}">Quitar</button>
                </td>`;
            body.appendChild(tr);
        });
        body.querySelectorAll('input[data-id]').forEach(i => {
            i.addEventListener('input', () => {
                const it = cart.get(i.dataset.id);
                let n = Math.max(1, Math.min(Number(i.value), it.stock));
                it.cant = n;
                i.value = n;
                recomputeTotals();
            });
        });
        body.querySelectorAll('button[data-rm]').forEach(b => {
            b.addEventListener('click', () => { cart.delete(b.dataset.rm); renderCart(); });
        });
        recomputeTotals();
        checkInteractions();
    }

    function checkInteractions() {
        const principios = [];
        cart.forEach(i => { if (i.principio) principios.push(i.principio); });
        
        if (principios.length < 2) {
            $('interactionAlerts').classList.add('hidden');
            return;
        }

        fetch(`{{ route('pos.interacciones') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ principios })
        })
        .then(r => r.json())
        .then(data => {
            if (data.length > 0) {
                $('interactionAlerts').classList.remove('hidden');
                $('interactionList').innerHTML = data.map(i => `
                    <li><strong>${i.principio_a} + ${i.principio_b} (${i.severidad}):</strong> ${i.descripcion}</li>
                `).join('');
            } else {
                $('interactionAlerts').classList.add('hidden');
            }
        });
    }

    function recomputeTotals() {
        let sub = 0;
        cart.forEach(i => sub += i.precio * i.cant);
        
        const descManual = Math.max(0, Number($('descuento').value) || 0);
        const descPuntos = Math.max(0, Number($('puntosCanje').value) || 0);
        const descTotal  = descManual + descPuntos;

        const base = Math.max(0, sub - descTotal);
        const imp  = Math.round(base * 0.18 * 100) / 100;
        const tot  = Math.round((base + imp) * 100) / 100;
        const pago = Number($('pago').value) || 0;
        const cam  = Math.max(0, pago - tot);

        $('sumSub').textContent = fmt(sub);
        $('sumDesc').textContent = fmt(descManual);
        $('sumPts').textContent = fmt(descPuntos);
        $('sumImp').textContent = fmt(imp);
        $('sumTot').textContent = fmt(tot);
        $('sumCam').textContent = fmt(cam);
    }

    $('cliente').addEventListener('change', () => {
        const opt = $('cliente').selectedOptions[0];
        const pts = Number(opt.dataset.puntos || 0);
        if (pts > 0) {
            $('puntosBox').classList.remove('hidden');
            $('puntosDisponibles').textContent = pts;
            $('puntosCanje').max = pts;
        } else {
            $('puntosBox').classList.add('hidden');
            $('puntosCanje').value = 0;
        }
        recomputeTotals();
    });

    ['descuento', 'pago', 'puntosCanje'].forEach(id => $(id).addEventListener('input', recomputeTotals));

    $('formaPago').addEventListener('change', () => {
        if ($('formaPago').value !== 'efectivo' && $('formaPago').value !== 'mixto') {
            const totStr = $('sumTot').textContent.replace('S/ ', '').replace(',', '');
            $('pago').value = parseFloat(totStr) || 0;
            $('pago').readOnly = true;
            $('pago').classList.add('bg-gray-50');
        } else {
            $('pago').readOnly = false;
            $('pago').classList.remove('bg-gray-50');
        }
        recomputeTotals();
    });

    // Atajos de teclado
    document.addEventListener('keydown', (e) => {
        if (e.key === 'F9') { e.preventDefault(); $('pago').focus(); $('pago').select(); }
        if (e.key === 'F12') { e.preventDefault(); $('btnPagar').click(); }
    });

    function setMsg(t, color = 'farmacia') {
        $('msg').textContent = t;
        $('msg').className = `text-xs mt-2 text-${color}-600`;
    }

    $('btnPagar').addEventListener('click', () => {
        if (cart.size === 0) return setMsg('Agrega productos al carrito.', 'rose');
        const items = [];
        cart.forEach(i => items.push({ producto_id: i.id, cantidad: i.cant, precio: i.precio }));

        fetch(`{{ route('pos.store') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({
                cliente_id: $('cliente').value || null,
                forma_pago: $('formaPago').value,
                pago_recibido: Number($('pago').value) || 0,
                descuento: Number($('descuento').value) || 0,
                puntos_canje: Number($('puntosCanje').value) || 0,
                observaciones: $('obs').value,
                items,
            }),
        })
        .then(r => r.json().then(j => ({ status: r.status, j })))
        .then(({ status, j }) => {
            if (status >= 400) {
                setMsg(j.message || 'Revisa los datos.', 'rose');
                return;
            }
            setMsg(`✔ Venta ${j.codigo} emitida. Cambio: ${fmt(j.cambio)}. Imprimiendo...`, 'emerald');
            if (j.ticket_url) {
                window.open(j.ticket_url, 'Ticket', 'width=400,height=600');
            }
            setTimeout(() => window.location.href = j.redirect, 1200);
        })
        .catch(() => setMsg('Error de red.', 'rose'));
    });
})();
</script>
@endpush
