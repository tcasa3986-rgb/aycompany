<x-app-layout>
    <x-slot name="title">Mantenimiento y Respaldos</x-slot>

    <div style="max-width:900px; margin:0 auto;">
        <div style="margin-bottom:24px;">
            <h1 style="font-size:24px; font-weight:700; color:var(--text); margin-bottom:8px;">Mantenimiento del Sistema</h1>
            <p style="color:var(--text-muted);">Gestiona las copias de seguridad de tu base de datos o restablece el sistema a sus valores de fábrica.</p>
        </div>

        <div style="display:grid; gap:20px; grid-template-columns:1fr;">
            
            {{-- Panel: Descargar Respaldo --}}
            <div class="card" style="border-left:4px solid var(--success);">
                <div style="display:flex; gap:20px; align-items:center; padding:10px;">
                    <div style="background:#dcfce7; padding:16px; border-radius:12px; color:#166534;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:32px; height:32px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div style="flex:1;">
                        <h3 style="font-size:18px; font-weight:600; margin-bottom:4px; color:var(--text);">Descargar Copia de Seguridad</h3>
                        <p style="color:var(--text-muted); font-size:14px; margin-bottom:0;">Genera y descarga un archivo (.sql) con toda la información actual de tu ERP: cotizaciones, clientes, productos y configuración. Guárdalo en un lugar seguro.</p>
                    </div>
                    <div>
                        <a href="{{ route('system.backup.download') }}" download="cotizapro_respaldo.sql" class="btn btn-primary" style="background:#16a34a; border-color:#16a34a; padding:10px 24px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Descargar Ahora
                        </a>
                    </div>
                </div>
            </div>

            {{-- Panel: Restaurar Respaldo --}}
            <div class="card" style="border-left:4px solid #f59e0b;">
                <form action="{{ route('system.backup.restore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="display:flex; gap:20px; align-items:flex-start; padding:10px;">
                        <div style="background:#fef3c7; padding:16px; border-radius:12px; color:#b45309;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:32px; height:32px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </div>
                        <div style="flex:1;">
                            <h3 style="font-size:18px; font-weight:600; margin-bottom:4px; color:var(--text);">Restaurar Copia de Seguridad</h3>
                            <p style="color:var(--text-muted); font-size:14px; margin-bottom:12px;">Sube un archivo de respaldo generado previamente por este sistema. <strong>Atención:</strong> Esto sobrescribirá toda tu información actual.</p>
                            
                            <div style="display:flex; gap:12px; align-items:center;">
                                <input type="file" name="backup_file" id="backup_file" accept=".sql,.sqlite" class="form-control" style="max-width:300px; padding:6px;" required>
                                <button type="button" onclick="confirmRestore()" class="btn btn-secondary" style="color:#b45309; border-color:#fcd34d; background:#fffbeb;">
                                    Restaurar Sistema
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Panel: Resetear Sistema --}}
            <div class="card" style="border-left:4px solid var(--danger);">
                <form id="reset-form" action="{{ route('system.reset') }}" method="POST">
                    @csrf
                    <div style="display:flex; gap:20px; align-items:center; padding:10px;">
                        <div style="background:#fee2e2; padding:16px; border-radius:12px; color:#b91c1c;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:32px; height:32px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div style="flex:1;">
                            <h3 style="font-size:18px; font-weight:600; margin-bottom:4px; color:#b91c1c;">Reset de Fábrica (Para Nueva Empresa)</h3>
                            <p style="color:var(--text-muted); font-size:14px; margin-bottom:0;">Esta opción vaciará todas las tablas operativas (cotizaciones, clientes, productos y empresas) y restaurará la configuración por defecto. <strong>Solo tu usuario se mantendrá intacto.</strong></p>
                        </div>
                        <div>
                            <button type="button" onclick="confirmReset()" class="btn btn-danger" style="padding:10px 24px;">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Vaciar Sistema
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        function confirmRestore() {
            const fileInput = document.getElementById('backup_file');
            if(!fileInput.value) {
                showToast('Por favor, selecciona un archivo de respaldo (.sql) primero', 'error');
                return;
            }

            if(confirm("⚠️ ADVERTENCIA: Estás a punto de reemplazar toda tu base de datos actual. Todo progreso no guardado en la copia de seguridad se perderá irremediablemente.\n\n¿Estás absolutamente seguro de continuar?")) {
                fileInput.closest('form').submit();
            }
        }

        function confirmReset() {
            const result = prompt("⚠️ ADVERTENCIA DE BORRADO MASIVO\n\nEstás a punto de vaciar todo el sistema. Se borrarán todas tus cotizaciones, clientes, productos y empresas. Solo se conservará tu usuario de acceso.\n\nPara confirmar, escribe exactamente la palabra: CONFIRMAR");
            
            if (result && result.trim().toUpperCase() === "CONFIRMAR") {
                document.getElementById('reset-form').submit();
            } else if (result !== null) {
                showToast("Palabra de confirmación incorrecta. No se hicieron cambios.", "error");
            }
        }
    </script>
</x-app-layout>
