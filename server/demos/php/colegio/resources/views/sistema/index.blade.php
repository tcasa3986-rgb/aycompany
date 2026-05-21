@extends('layouts.app')
@section('title', 'Gestión del Sistema')
@section('page-title', 'Mantenimiento y Respaldo')

@section('content')

<div class="grid grid-3" style="gap:24px;align-items:start;">

    {{-- COLUMNA 1: BACKUP --}}
    <div class="card" style="border-top: 4px solid #10b981;">
        <div class="card-header">
            <span class="card-title">
                <i class="fas fa-download" style="color:#10b981;margin-right:8px;"></i>
                Copia de Seguridad
            </span>
        </div>
        <div class="card-body" style="text-align:center;padding:32px 24px;">
            <div style="font-size:48px;color:#d1fae5;margin-bottom:16px;">
                <i class="fas fa-database"></i>
            </div>
            <h3 style="margin-bottom:12px;">Generar Backup</h3>
            <p style="color:var(--muted);font-size:14px;margin-bottom:24px;line-height:1.5;">
                Descarga una copia completa de toda la información actual de tu base de datos, incluyendo alumnos, pagos, notas y configuraciones.
            </p>
            
            <form method="POST" action="{{ route('sistema.backup') }}">
                @csrf
                <button type="submit" class="btn btn-success" style="width:100%;padding:12px;font-size:15px;background-color:#10b981;border:none;">
                    <i class="fas fa-cloud-download-alt" style="margin-right:8px;"></i> Descargar .SQL Ahora
                </button>
            </form>
        </div>
    </div>

    {{-- COLUMNA 2: RESTAURACIÓN --}}
    <div class="card" style="border-top: 4px solid #3b82f6;">
        <div class="card-header">
            <span class="card-title">
                <i class="fas fa-upload" style="color:#3b82f6;margin-right:8px;"></i>
                Restaurar Sistema
            </span>
        </div>
        <div class="card-body" style="text-align:center;padding:32px 24px;">
            <div style="font-size:48px;color:#dbeafe;margin-bottom:16px;">
                <i class="fas fa-sync"></i>
            </div>
            <h3 style="margin-bottom:12px;">Subir Backup</h3>
            <p style="color:var(--muted);font-size:14px;margin-bottom:24px;line-height:1.5;">
                Restaura el sistema a un punto anterior subiendo un archivo <strong>.sql</strong> previamente descargado. Esto reemplazará los datos actuales.
            </p>
            
            <form method="POST" action="{{ route('sistema.restore') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group" style="text-align:left;">
                    <input type="file" name="backup_file" class="form-control" accept=".sql" required style="margin-bottom:16px;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:15px;" onclick="return confirm('¿Estás seguro de querer sobreescribir la base de datos actual? Esta acción no se puede deshacer.')">
                    <i class="fas fa-history" style="margin-right:8px;"></i> Iniciar Restauración
                </button>
            </form>
        </div>
    </div>

    {{-- COLUMNA 3: RESET (ZONA DE PELIGRO) --}}
    <div class="card" style="border-top: 4px solid #ef4444; background-color:#fef2f2;">
        <div class="card-header" style="border-bottom-color:#fecaca;">
            <span class="card-title" style="color:#b91c1c;">
                <i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i>
                Zona de Peligro
            </span>
        </div>
        <div class="card-body" style="text-align:center;padding:32px 24px;">
            <div style="font-size:48px;color:#fecaca;margin-bottom:16px;">
                <i class="fas fa-skull-crossbones"></i>
            </div>
            <h3 style="margin-bottom:12px;color:#991b1b;">Resetear Sistema</h3>
            <p style="color:#b91c1c;font-size:14px;margin-bottom:24px;line-height:1.5;">
                Eliminará <strong>todos los registros</strong> (alumnos, matrículas, pagos, notas) y dejará el sistema en blanco para iniciar un nuevo periodo o empresa.
            </p>
            
            <form method="POST" action="{{ route('sistema.reset') }}">
                @csrf
                <div class="form-group" style="text-align:left;">
                    <label class="form-label" style="color:#991b1b;">Escribe "RESETEAR" para confirmar:</label>
                    <input type="text" name="confirm_text" class="form-control" required style="border-color:#fca5a5;background:#fff;" placeholder="RESETEAR" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-danger" style="width:100%;padding:12px;font-size:15px;background-color:#ef4444;border:none;">
                    <i class="fas fa-trash-alt" style="margin-right:8px;"></i> Borrar Todos los Datos
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
