<x-app-layout>
    <x-slot name="title">Nueva Cotización</x-slot>

    <form method="POST" action="{{ route('quotations.store') }}" id="quotation-form">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

            {{-- ══ COLUMNA IZQUIERDA ══ --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Datos cabecera --}}
                <div class="card">
                    <div class="card-header"><span class="card-title">Datos de la Cotización</span></div>
                    <div class="form-row cols-3">
                        <div class="form-group">
                            <label class="form-label">Número *</label>
                            <input type="text" name="quotation_number" class="form-control"
                                   value="{{ old('quotation_number', $number) }}" required>
                            @error('quotation_number')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha de Emisión *</label>
                            <input type="date" name="issue_date" id="issue_date" class="form-control"
                                   value="{{ old('issue_date', date('Y-m-d')) }}" required
                                   onchange="autoFillDueDate()">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Vencimiento</label>
                            <input type="date" name="due_date" id="due_date" class="form-control"
                                   value="{{ old('due_date') }}">
                            <div style="font-size:10.5px;color:var(--text-muted);margin-top:3px">Auto: emisión + {{ $defaultValidityDays ?? 30 }} días</div>
                        </div>
                    </div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label class="form-label">Cliente *</label>
                            <select name="client_id" class="form-control" required>
                                <option value="">Seleccionar cliente...</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}{{ $c->document_number ? ' — '.$c->document_number : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Moneda *</label>
                            <select name="currency" class="form-control" required id="currency-select">
                                <option value="PEN" {{ old('currency', $defaultCurrency) === 'PEN' ? 'selected' : '' }}>🇵🇪 PEN — Sol</option>
                                <option value="USD" {{ old('currency', $defaultCurrency) === 'USD' ? 'selected' : '' }}>🇺🇸 USD — Dólar</option>
                                <option value="EUR" {{ old('currency', $defaultCurrency) === 'EUR' ? 'selected' : '' }}>🇪🇺 EUR — Euro</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notas / Observaciones</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Líneas de detalle --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Líneas de Detalle</span>
                        <button type="button" class="btn btn-secondary btn-sm" id="add-line">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar Línea
                        </button>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:30%">Producto / Descripción</th>
                                    <th style="width:8%">Unidad</th>
                                    <th style="width:9%">Cant.</th>
                                    <th style="width:14%">P. Unit.</th>
                                    <th style="width:9%">Dto. %</th>
                                    <th style="width:14%">Subtotal</th>
                                    <th style="width:6%"></th>
                                </tr>
                            </thead>
                            <tbody id="details-body"></tbody>
                        </table>
                    </div>
                    @error('details')<div class="form-error" style="margin-top:8px">{{ $message }}</div>@enderror
                </div>

                {{-- Términos --}}
                <div class="card">
                    <div class="card-header"><span class="card-title">Términos y Condiciones</span></div>
                    <textarea name="terms" class="form-control" rows="5">{{ old('terms', $defaultTerms) }}</textarea>
                </div>
            </div>

            {{-- ══ COLUMNA DERECHA — Totales ══ --}}
            <div class="card" style="position:sticky;top:80px;">
                <div class="card-header"><span class="card-title">Resumen</span></div>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:var(--text-muted);font-size:13px;">Subtotal bruto</span>
                        <span id="display-subtotal" style="font-weight:600;">— 0.00</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:var(--text-muted);font-size:13px;">Dto. por líneas</span>
                        <span id="display-line-discount" style="font-weight:600;color:#f87171;">— 0.00</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="color:var(--text-muted);font-size:13px;">Dto. global %</span>
                            <input type="number" id="global-discount" name="global_discount_pct" min="0" max="100"
                                   style="width:55px;" class="form-control" value="{{ old('global_discount_pct', 0) }}">
                        </div>
                        <span id="display-global-discount" style="font-weight:600;color:#f87171;">— 0.00</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="color:var(--text-muted);font-size:13px;">IGV %</span>
                            <input type="number" id="tax-rate" name="tax_rate" min="0" max="100"
                                   style="width:55px;" class="form-control" value="{{ old('tax_rate', $defaultTaxRate) }}">
                        </div>
                        <span id="display-tax" style="font-weight:600;">— 0.00</span>
                    </div>
                    <div style="border-top:1px solid var(--border);padding-top:12px;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:15px;font-weight:700;">Total</span>
                        <span id="display-total" style="font-size:22px;font-weight:800;color:var(--accent);">— 0.00</span>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;flex-direction:column;gap:8px;">
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                        Guardar Cotización
                    </button>
                    <a href="{{ route('quotations.index') }}" class="btn btn-secondary" style="width:100%;justify-content:center;">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>


    @php
        $productsJson = $products->map(function($p) {
            return ['id' => $p->id, 'name' => $p->name, 'price' => $p->price, 'unit' => $p->unit ?? ''];
        });
    @endphp
    <script>
    (function () {
        const products = @json($productsJson);

        const currencies = { PEN: 'S/', USD: '$', EUR: '€' };
        let rowIndex = 0;

        function sym() {
            const c = document.getElementById('currency-select').value;
            return currencies[c] || '{{ $globalSym }}';
        }

        function fmt(n) { return sym() + ' ' + parseFloat(n).toFixed(2); }

        function recalc() {
            let grossSub    = 0;
            let lineDisc    = 0;

            document.querySelectorAll('.detail-row').forEach(row => {
                const qty      = parseFloat(row.querySelector('.qty').value)   || 0;
                const price    = parseFloat(row.querySelector('.price').value)  || 0;
                const disc     = parseFloat(row.querySelector('.disc').value)   || 0;
                const base     = qty * price;
                const discAmt  = base * disc / 100;
                const st       = base - discAmt;
                row.querySelector('.subtotal').textContent = fmt(st);
                grossSub  += base;
                lineDisc  += discAmt;
            });

            const netSub       = grossSub - lineDisc;
            const globalDiscPct = parseFloat(document.getElementById('global-discount').value) || 0;
            const globalDisc    = netSub * globalDiscPct / 100;
            const base          = netSub - globalDisc;
            const taxRate       = parseFloat(document.getElementById('tax-rate').value) || 0;
            const tax           = base * taxRate / 100;
            const total         = base + tax;
            const s             = sym();

            document.getElementById('display-subtotal').textContent        = s + ' ' + grossSub.toFixed(2);
            document.getElementById('display-line-discount').textContent   = '- ' + s + ' ' + lineDisc.toFixed(2);
            document.getElementById('display-global-discount').textContent = '- ' + s + ' ' + globalDisc.toFixed(2);
            document.getElementById('display-tax').textContent             = s + ' ' + tax.toFixed(2);
            document.getElementById('display-total').textContent           = s + ' ' + total.toFixed(2);
        }

        function addRow(data) {
            const i  = rowIndex++;
            const tr = document.createElement('tr');
            tr.className = 'detail-row';
            tr.innerHTML = `
                <td>
                    <select class="form-control product-select" name="details[${i}][product_id]" style="margin-bottom:4px;font-size:12px">
                        <option value="">— Personalizado —</option>
                        ${products.map(p => `<option value="${p.id}" data-price="${p.price}" data-unit="${p.unit}"
                            ${data && data.product_id == p.id ? 'selected' : ''}>${p.name}</option>`).join('')}
                    </select>
                    <input type="text" class="form-control" name="details[${i}][product_name]"
                           placeholder="Descripción del ítem" required
                           value="${data ? data.product_name : ''}" style="font-size:12px">
                </td>
                <td>
                    <input type="text" class="form-control" name="details[${i}][unit]"
                           placeholder="und" style="font-size:12px"
                           value="${data ? (data.unit ?? '') : ''}">
                </td>
                <td>
                    <input type="number" class="form-control qty" name="details[${i}][quantity]"
                           min="0.001" step="0.001" value="${data ? data.quantity : 1}" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control price" name="details[${i}][unit_price]"
                           min="0" value="${data ? data.unit_price : '0.00'}" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control disc" name="details[${i}][discount_pct]"
                           min="0" max="100" value="${data ? (data.discount_pct ?? 0) : 0}" placeholder="0">
                </td>
                <td class="subtotal" style="font-weight:600;white-space:nowrap;">${fmt(data ? data.quantity * data.unit_price * (1 - (data.discount_pct ?? 0) / 100) : 0)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-btn">✕</button>
                </td>`;

            tr.querySelector('.product-select').addEventListener('change', function () {
                const opt   = this.options[this.selectedIndex];
                const price = opt.dataset.price;
                const unit  = opt.dataset.unit;
                if (price) {
                    tr.querySelector('.price').value = price;
                    const name = opt.textContent.trim();
                    tr.querySelector('[name$="[product_name]"]').value = name === '— Personalizado —' ? '' : name;
                }
                if (unit) tr.querySelector('[name$="[unit]"]').value = unit;
                recalc();
            });
            ['qty', 'price', 'disc'].forEach(cls => tr.querySelector('.' + cls).addEventListener('input', recalc));
            tr.querySelector('.remove-btn').addEventListener('click', () => { tr.remove(); recalc(); });

            document.getElementById('details-body').appendChild(tr);
            recalc();
        }

        document.getElementById('add-line').addEventListener('click', () => addRow(null));
        document.getElementById('tax-rate').addEventListener('input', recalc);
        document.getElementById('global-discount').addEventListener('input', recalc);
        document.getElementById('currency-select').addEventListener('change', recalc);

        addRow(null);

        // Auto due date
        autoFillDueDate();
    })();

    function autoFillDueDate() {
        const issueInput = document.getElementById('issue_date');
        const dueInput   = document.getElementById('due_date');
        if (!issueInput || !dueInput || dueInput.value) return;
        const days   = {{ $defaultValidityDays ?? 30 }};
        const date   = new Date(issueInput.value);
        if (isNaN(date.getTime())) return;
        date.setDate(date.getDate() + days);
        dueInput.value = date.toISOString().split('T')[0];
    }
    </script>
</x-app-layout>
