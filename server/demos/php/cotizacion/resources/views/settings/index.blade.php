<x-app-layout>
    <x-slot name="title">Configuración del Sistema</x-slot>

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

            {{-- Empresa Emisora --}}
            <div class="card" style="grid-column:1/2">
                <div class="card-header">
                    <div>
                        <div class="card-title">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;display:inline;margin-right:6px;color:var(--accent)"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Datos de la Empresa Emisora
                        </div>
                        <div class="card-sub">Aparecen en el encabezado de cada cotización PDF</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre / Razón Social *</label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required placeholder="Mi Empresa S.A.C.">
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">RUC / NIT / RFC</label>
                        <input type="text" name="company_ruc" class="form-control" value="{{ old('company_ruc', $settings['company_ruc'] ?? '') }}" placeholder="20512345678">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" placeholder="+51 01 234-5678">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="company_address" class="form-control" value="{{ old('company_address', $settings['company_address'] ?? '') }}" placeholder="Av. Principal 123, Lima">
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $settings['company_email'] ?? '') }}" placeholder="ventas@empresa.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sitio Web</label>
                        <input type="text" name="company_website" class="form-control" value="{{ old('company_website', $settings['company_website'] ?? '') }}" placeholder="https://www.empresa.com">
                    </div>
                </div>
            </div>

            {{-- Parámetros de cotización --}}
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;display:inline;margin-right:6px;color:var(--accent)"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Parámetros de Cotización
                            </div>
                        </div>
                    </div>

                    <div class="form-row cols-3">
                        <div class="form-group">
                            <label class="form-label">Prefijo *</label>
                            <input type="text" name="quotation_prefix" class="form-control" value="{{ old('quotation_prefix', $settings['quotation_prefix'] ?? 'COT') }}" required maxlength="10" placeholder="COT">
                            <div style="font-size:10.5px;color:var(--text-muted);margin-top:4px">Formato: PREFIJO-AÑO-NNNN</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Moneda por Defecto *</label>
                            <select name="default_currency" class="form-control" required>
                                @foreach(['PEN'=>'🇵🇪 PEN — Sol','USD'=>'🇺🇸 USD — Dólar','EUR'=>'🇪🇺 EUR — Euro'] as $code => $label)
                                    <option value="{{ $code }}" {{ ($settings['default_currency'] ?? 'PEN') == $code ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">IGV / IVA por Defecto %</label>
                            <input type="number" name="default_tax_rate" class="form-control" step="0.01" min="0" max="100" value="{{ old('default_tax_rate', $settings['default_tax_rate'] ?? '18') }}" placeholder="18">
                        </div>
                    </div>

                    {{-- Número de días de validez --}}
                    <div class="form-group">
                        <label class="form-label">Días de Validez por Defecto</label>
                        <input type="number" name="default_validity_days" class="form-control" min="0" max="365" value="{{ old('default_validity_days', $settings['default_validity_days'] ?? '30') }}" placeholder="30">
                        <div style="font-size:10.5px;color:var(--text-muted);margin-top:4px">Se usará para calcular la fecha de vencimiento automáticamente</div>
                    </div>
                </div>

                {{-- Términos y Condiciones --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Términos y Condiciones por Defecto</div>
                        <div class="card-sub">Se pre-cargará en cada nueva cotización</div>
                    </div>
                    <div class="form-group">
                        <textarea name="terms_and_conditions" class="form-control" rows="6" placeholder="1. Los precios son válidos por 30 días...">{{ old('terms_and_conditions', $settings['terms_and_conditions'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Logo Upload --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <div>
                    <div class="card-title">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;display:inline;margin-right:6px;color:var(--accent)"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Logo de Empresa en PDF
                    </div>
                    <div class="card-sub">Se mostrará en el encabezado de cada cotización PDF · Formato PNG recomendado</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                {{-- Preview --}}
                <div id="logoPreviewWrap" style="width:120px;height:80px;border:2px dashed var(--border);border-radius:10px;display:flex;align-items:center;justify-content:center;background:#f8fafc;overflow:hidden;flex-shrink:0;">
                    @if($hasLogo)
                        <img id="logoPreview" src="{{ asset('logo.png') }}?t={{ time() }}" style="max-width:116px;max-height:76px;object-fit:contain;">
                    @else
                        <img id="logoPreview" src="" style="max-width:116px;max-height:76px;object-fit:contain;display:none;">
                        <span id="logoPlaceholder" style="font-size:11px;color:var(--text-muted);text-align:center;padding:8px;">Sin logo</span>
                    @endif
                </div>
                <div>
                    <div style="margin-bottom:10px;">
                        <label class="btn btn-secondary" for="logoInput" style="cursor:pointer;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Subir logo (PNG/JPG · Máx 2MB)
                        </label>
                        <input type="file" id="logoInput" name="logo" accept="image/*" style="display:none;" onchange="previewLogo(this)">
                    </div>
                    @if($hasLogo)
                    <label style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:var(--danger);cursor:pointer;">
                        <input type="checkbox" name="delete_logo" value="1"> Eliminar logo actual
                    </label>
                    @endif
                    <div style="font-size:11px;color:var(--text-muted);margin-top:6px;">Recomendado: fondo transparente, ancho ≥ 300px</div>
                </div>
            </div>
        </div>

        {{-- Configuración SMTP --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <div style="display:flex;justify-content:space-between;align-items:center;width:100%;">
                    <div>
                        <div class="card-title">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;display:inline;margin-right:6px;color:var(--accent)"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Configuración de Correo (SMTP)
                        </div>
                        <div class="card-sub">Necesario para enviar cotizaciones por email directamente desde el sistema</div>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="testEmail()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Probar Conexión
                    </button>
                </div>
            </div>

            <div class="form-row cols-3">
                <div class="form-group">
                    <label class="form-label">Servidor (Host)</label>
                    <input type="text" name="smtp_host" class="form-control" value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}" placeholder="smtp.mailtrap.io">
                </div>
                <div class="form-group">
                    <label class="form-label">Puerto (Port)</label>
                    <input type="text" name="smtp_port" class="form-control" value="{{ old('smtp_port', $settings['smtp_port'] ?? '587') }}" placeholder="587">
                </div>
                <div class="form-group">
                    <label class="form-label">Encriptación</label>
                    <select name="smtp_encryption" class="form-control">
                        <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['smtp_encryption'] ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="" {{ ($settings['smtp_encryption'] ?? 'tls') == '' ? 'selected' : '' }}>Ninguna</option>
                    </select>
                </div>
            </div>

            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label">Usuario (Username)</label>
                    <input type="text" name="smtp_username" class="form-control" value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}" placeholder="usuario@empresa.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña (Password)</label>
                    <input type="password" name="smtp_password" class="form-control" value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}" placeholder="••••••••">
                </div>
            </div>

            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label">Correo de Envío (From Address)</label>
                    <input type="email" name="smtp_from_address" class="form-control" value="{{ old('smtp_from_address', $settings['smtp_from_address'] ?? '') }}" placeholder="cotizaciones@empresa.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre de Envío (From Name)</label>
                    <input type="text" name="smtp_from_name" class="form-control" value="{{ old('smtp_from_name', $settings['smtp_from_name'] ?? '') }}" placeholder="Mi Empresa Ventas">
                </div>
            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary" style="padding:11px 28px;font-size:14px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Guardar Configuración
            </button>
        </div>
    </form>

    <script>
    function previewLogo(input) {
        const preview   = document.getElementById('logoPreview');
        const placeholder = document.getElementById('logoPlaceholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    async function testEmail() {
        const form = document.querySelector('form');
        const formData = new FormData(form);
        const btn = event.currentTarget;
        const originalContent = btn.innerHTML;
        btn.innerHTML = 'Probando...';
        btn.disabled = true;

        try {
            const res = await fetch('{{ route('settings.test-email') }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if(data.success) {
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Error al conectar', 'error');
            }
        } catch (e) {
            showToast('Error de conexión con el servidor', 'error');
        } finally {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    }
    </script>
</x-app-layout>
