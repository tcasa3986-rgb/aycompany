@extends('layouts.app')

@section('title', 'Mantenimiento del Sistema')

@push('styles')
<style>
    /* Estilos específicos para la vista de mantenimiento */
    .file-upload-area {
        border: 2px dashed var(--border-strong);
        border-radius: var(--radius-md);
        padding: 40px 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .file-upload-area:hover {
        border-color: var(--accent-primary);
        background: var(--accent-light);
    }

    .file-upload-area i {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 15px;
    }

    .file-upload-area:hover i {
        color: var(--accent-primary);
    }

    .alert {
        padding: 15px 20px;
        border-radius: var(--radius-md);
        margin-bottom: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #34d399; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
    
    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 25px;
        background: rgba(255, 255, 255, 0.5);
        padding: 20px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
    }

    .checkbox-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .checkbox-item input[type="checkbox"] {
        margin-top: 4px;
        width: 18px;
        height: 18px;
        accent-color: var(--danger);
        cursor: pointer;
    }

    .checkbox-item label {
        margin-bottom: 0;
        cursor: pointer;
        font-weight: 500;
        color: var(--text-primary);
    }

    .checkbox-item p {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 2px;
    }
    
    .danger-btn {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
    }

    .danger-btn:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Mantenimiento del Sistema</h1>
        <p class="text-secondary">Gestione respaldos, restaure datos y reinicie el sistema para nuevas sucursales.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('sistema.manual') }}" class="btn btn-secondary">
            <i class="fa-solid fa-file-pdf"></i> Descargar Manual Técnico
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="fa-solid fa-triangle-exclamation"></i>
        {{ session('error') }}
    </div>
@endif

<div class="dashboard-grid">

    <!-- COPIA DE SEGURIDAD -->
    <div class="col-4">
        <div class="card kpi-blue h-100" style="display: flex; flex-direction: column;">
            <div class="card-header" style="border-bottom-color: rgba(2, 132, 199, 0.2);">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="card-icon">
                        <i class="fa-solid fa-cloud-arrow-down"></i>
                    </div>
                    <h3 class="card-title" style="margin: 0; color: #0284c7;">Copia de Seguridad</h3>
                </div>
            </div>
            
            <div style="flex: 1; padding: 10px 0;">
                <p style="color: #0369a1; margin-bottom: 20px;">
                    Crea un respaldo de seguridad instantáneo de toda la base de datos (estructura y registros).
                    Recomendamos hacer esto al menos una vez por semana.
                </p>
                <form action="{{ route('sistema.backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn" style="background: #0284c7; color: white; width: 100%;">
                        <i class="fa-solid fa-download"></i> Descargar Backup (.sql)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- RESTAURAR SISTEMA -->
    <div class="col-4">
        <div class="card kpi-teal h-100" style="display: flex; flex-direction: column;">
            <div class="card-header" style="border-bottom-color: rgba(15, 118, 110, 0.2);">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="card-icon">
                        <i class="fa-solid fa-rotate-left"></i>
                    </div>
                    <h3 class="card-title" style="margin: 0; color: #0f766e;">Restaurar Sistema</h3>
                </div>
            </div>
            
            <div style="flex: 1;">
                <p style="color: #0f766e; margin-bottom: 15px;">
                    Seleccione un archivo de copia de seguridad <b>.sql</b> previamente generado.
                </p>
                <form action="{{ route('sistema.restore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="file-upload-area" for="backup_file" style="border-color: #5eead4; background: rgba(255,255,255,0.4);">
                        <i class="fa-solid fa-file-sql" style="color: #0d9488;"></i>
                        <h4 style="color: #0f766e; font-size: 1rem; margin-bottom: 5px;" id="file-name-display">Haz clic para subir tu archivo SQL</h4>
                        <p style="color: #0f766e; font-size: 0.8rem;">Solo archivos de extensión .sql</p>
                    </label>
                    <input type="file" name="backup_file" id="backup_file" accept=".sql" style="display: none;" onchange="updateFileName(this)">
                    
                    <button type="submit" class="btn" style="background: #0d9488; color: white; width: 100%;" onclick="return confirm('ATENCIÓN: Esto reescribirá la base de datos completa. ¿Estás seguro de que este es el archivo correcto?');">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Iniciar Restauración
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- RESETEAR PARA EMPRESA NUEVA -->
    <div class="col-4">
        <div class="card kpi-amber h-100" style="display: flex; flex-direction: column;">
            <div class="card-header" style="border-bottom-color: rgba(180, 83, 9, 0.2);">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="card-icon">
                        <i class="fa-solid fa-power-off"></i>
                    </div>
                    <h3 class="card-title" style="margin: 0; color: #b45309;">Resetear Sistema</h3>
                </div>
            </div>
            
            <div style="flex: 1;">
                <p style="color: #b45309; margin-bottom: 15px;">
                    Elimine los datos seleccionados para iniciar una nueva empresa. <b>(Los usuarios y configuraciones se conservarán)</b>.
                </p>
                <form action="{{ route('sistema.reset') }}" method="POST" onsubmit="return confirm('⚠ ADVERTENCIA CRÍTICA ⚠\n\nEstá a punto de borrar definitivamente los datos marcados. Esta acción NO se puede deshacer.\n\n¿Desea continuar?');">
                    @csrf
                    <div class="checkbox-group" style="border-color: #fcd34d;">
                        
                        <div class="checkbox-item">
                            <input type="checkbox" id="clear_transactions" name="clear_transactions" value="1" checked>
                            <div>
                                <label for="clear_transactions" style="color: #92400e;">Datos Transaccionales</label>
                                <p style="color: #b45309;">Borrará todas las Órdenes, Resultados, Muestras, Pagos y Facturas.</p>
                            </div>
                        </div>

                        <div class="checkbox-item">
                            <input type="checkbox" id="clear_patients" name="clear_patients" value="1" checked>
                            <div>
                                <label for="clear_patients" style="color: #92400e;">Directorio de Pacientes</label>
                                <p style="color: #b45309;">Vaciara el directorio completo de pacientes y sus antecedentes.</p>
                            </div>
                        </div>
                        
                        <div class="checkbox-item">
                            <input type="checkbox" id="clear_resources" name="clear_resources" value="1">
                            <div>
                                <label for="clear_resources" style="color: #92400e;">Médicos y Convenios</label>
                                <p style="color: #b45309;">Borrara los médicos referidores y empresas aliadas.</p>
                            </div>
                        </div>

                        <div class="checkbox-item">
                            <input type="checkbox" id="clear_catalog" name="clear_catalog" value="1">
                            <div>
                                <label for="clear_catalog" style="color: #92400e;">Catálogo Completo</label>
                                <p style="color: #b45309;">Eliminara las Áreas de Laboratorio, Pruebas clínicas e Inventario de Reactivos.</p>
                            </div>
                        </div>

                    </div>
                    
                    <button type="submit" class="btn danger-btn" style="width: 100%;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Ejecutar Reseteo
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            let displayElement = document.getElementById('file-name-display');
            displayElement.innerText = "Archivo listado: " + input.files[0].name;
        }
    }
</script>
@endpush
