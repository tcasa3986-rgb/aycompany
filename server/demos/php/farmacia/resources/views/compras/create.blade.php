@extends('layouts.app')
@section('title', 'Nueva orden de compra')
@section('section', 'Compras')

@section('content')
<form method="POST" action="{{ route('compras.store') }}" id="compraForm">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="card card-pad lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Detalle de la orden</h2>

            <div class="mb-3 flex gap-2 items-end">
                <div class="flex-1">
                    <label class="label">Agregar producto</label>
                    <select id="prodSel" class="input">
                        <option value="">— Selecciona un producto —</option>
                        @foreach ($productos as $p)
                            <option value="{{ $p->id }}" data-nombre="{{ $p->nombre }}" data-precio="{{ $p->precio_compra }}">
                                {{ $p->codigo }} · {{ $p->nombre }} (S/ {{ $p->precio_compra }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="btnAdd" class="btn-secondary mb-0.5">
                    <x-icon name="plus" class="h-4 w-4 mr-1" /> Agregar
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead><tr>
                        <th>Producto</th>
                        <th class="w-24">Cant.</th>
                        <th class="w-32">P. Compra</th>
                        <th class="w-44">N° Lote</th>
                        <th class="w-44">Vence</th>
                        <th class="text-right">Subtotal</th>
                        <th></th>
                    </tr></thead>
                    <tbody id="rows">
                        <tr><td colspan="7" class="py-6 text-center text-gray-400">Sin productos agregados</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card card-pad">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Datos de la compra</h2>

            <div class="space-y-3">
                <div>
                    <label class="label">Proveedor *</label>
                    <select name="proveedor_id" class="input" required>
                        <option value="">— Seleccionar —</option>
                        @foreach ($proveedores as $p)
                            <option value="{{ $p->id }}">{{ $p->razon_social }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Observaciones</label>
                    <textarea name="observaciones" rows="3" class="input"></textarea>
                </div>
            </div>

            <div class="mt-5 border-t border-gray-100 pt-4 space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span> <span id="sumSub">S/ 0.00</span></div>
                <div class="flex justify-between"><span class="text-gray-500">IGV (18%)</span> <span id="sumImp">S/ 0.00</span></div>
                <div class="flex justify-between text-lg font-bold text-farmacia-700 mt-2">
                    <span>TOTAL</span> <span id="sumTot">S/ 0.00</span>
                </div>
            </div>

            <div class="mt-5 flex flex-col gap-2">
                <button type="submit" class="btn-primary">Guardar orden</button>
                <a href="{{ route('compras.index') }}" class="btn-secondary text-center">Cancelar</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    const fmt = n => 'S/ ' + Number(n || 0).toFixed(2);
    const $   = id => document.getElementById(id);
    const tbody = $('rows');
    let i = 0;

    $('btnAdd').addEventListener('click', () => {
        const sel = $('prodSel');
        const opt = sel.options[sel.selectedIndex];
        if (!opt.value) return;

        const idx = i++;
        if (tbody.querySelector('td[colspan]')) tbody.innerHTML = '';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="font-medium text-gray-800">
                ${opt.dataset.nombre}
                <input type="hidden" name="items[${idx}][producto_id]" value="${opt.value}">
            </td>
            <td><input type="number" name="items[${idx}][cantidad]" min="1" value="1" class="input w-20 text-right qty"></td>
            <td><input type="number" name="items[${idx}][precio_unitario]" step="0.01" min="0" value="${opt.dataset.precio}" class="input w-28 text-right pre"></td>
            <td><input type="text" name="items[${idx}][numero_lote]" class="input w-40" placeholder="opcional"></td>
            <td><input type="date" name="items[${idx}][fecha_vencimiento]" class="input w-40"></td>
            <td class="text-right font-semibold sub">S/ 0.00</td>
            <td class="text-right">
                <button type="button" class="text-rose-500 text-xs hover:underline rm">Quitar</button>
            </td>`;
        tbody.appendChild(tr);

        const recompRow = () => {
            const c = Number(tr.querySelector('.qty').value) || 0;
            const p = Number(tr.querySelector('.pre').value) || 0;
            tr.querySelector('.sub').textContent = fmt(c * p);
            recomputeTotals();
        };
        tr.querySelector('.qty').addEventListener('input', recompRow);
        tr.querySelector('.pre').addEventListener('input', recompRow);
        tr.querySelector('.rm').addEventListener('click', () => {
            tr.remove();
            if (!tbody.children.length) tbody.innerHTML = '<tr><td colspan="7" class="py-6 text-center text-gray-400">Sin productos agregados</td></tr>';
            recomputeTotals();
        });
        recompRow();

        sel.value = '';
    });

    function recomputeTotals() {
        let sub = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            const q = tr.querySelector('.qty'); if (!q) return;
            sub += (Number(q.value) || 0) * (Number(tr.querySelector('.pre').value) || 0);
        });
        const imp = Math.round(sub * 0.18 * 100) / 100;
        $('sumSub').textContent = fmt(sub);
        $('sumImp').textContent = fmt(imp);
        $('sumTot').textContent = fmt(sub + imp);
    }
})();
</script>
@endpush
