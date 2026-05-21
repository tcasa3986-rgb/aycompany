@extends('layouts.app')
@section('title', 'Backup y Mantenimiento')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Backup y Mantenimiento</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-shield-lock me-2 text-primary"></i>Backup y Mantenimiento</h4>
</div>

{{-- Stats del sistema --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2"><div class="card text-center h-100"><div class="card-body py-3">
        <i class="bi bi-people text-primary fs-2"></i>
        <div class="text-muted small">Clientes</div>
        <h5 class="mb-0">{{ number_format($stats['clientes']) }}</h5>
    </div></div></div>
    <div class="col-6 col-md-2"><div class="card text-center h-100"><div class="card-body py-3">
        <i class="bi bi-grid text-success fs-2"></i>
        <div class="text-muted small">Productos</div>
        <h5 class="mb-0">{{ number_format($stats['productos']) }}</h5>
    </div></div></div>
    <div class="col-6 col-md-2"><div class="card text-center h-100"><div class="card-body py-3">
        <i class="bi bi-receipt text-warning fs-2"></i>
        <div class="text-muted small">Pedidos</div>
        <h5 class="mb-0">{{ number_format($stats['pedidos']) }}</h5>
    </div></div></div>
    <div class="col-6 col-md-2"><div class="card text-center h-100"><div class="card-body py-3">
        <i class="bi bi-bicycle text-info fs-2"></i>
        <div class="text-muted small">Repartidores</div>
        <h5 class="mb-0">{{ number_format($stats['repartidores']) }}</h5>
    </div></div></div>
    <div class="col-6 col-md-2"><div class="card text-center h-100"><div class="card-body py-3">
        <i class="bi bi-person-badge text-secondary fs-2"></i>
        <div class="text-muted small">Usuarios</div>
        <h5 class="mb-0">{{ number_format($stats['usuarios']) }}</h5>
    </div></div></div>
    <div class="col-6 col-md-2"><div class="card text-center h-100 border-primary"><div class="card-body py-3">
        <i class="bi bi-database text-primary fs-2"></i>
        <div class="text-muted small">Tamaño BD</div>
        <h5 class="mb-0 text-primary">{{ $stats['tamano_bd'] }}</h5>
    </div></div></div>
</div>

<div class="row g-3">
    {{-- ============== BACKUP ============== --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100" style="border-top:4px solid #0d6efd">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary-subtle p-3 me-3">
                        <i class="bi bi-cloud-download text-primary fs-2"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Generar Copia de Seguridad</h5>
                        <small class="text-muted">Exporta toda la base de datos a un archivo SQL.</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('backups.crear') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Descripción (opcional)</label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Ej: Antes de actualizar" maxlength="120">
                    </div>
                    <button type="submit" class="btn btn-primary w-100" onclick="this.disabled=true;this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Generando...';this.form.submit();">
                        <i class="bi bi-cloud-download me-1"></i>Crear Backup Ahora
                    </button>
                </form>

                <hr>
                <h6 class="text-muted mb-2"><i class="bi bi-folder me-1"></i>Backups disponibles ({{ $files->count() }})</h6>
                @if($files->isEmpty())
                    <div class="text-center text-muted py-3 small">
                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                        Aún no hay backups generados.
                    </div>
                @else
                    <div class="list-group list-group-flush" style="max-height:280px;overflow-y:auto">
                        @foreach($files as $f)
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-file-earmark-code text-secondary me-1"></i>
                                <span class="small fw-semibold">{{ $f['nombre'] }}</span>
                                <div class="text-muted small">{{ $f['fecha']->format('d/m/Y H:i') }} — {{ $f['tamaño'] }}</div>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('backups.descargar', $f['nombre']) }}" class="btn btn-sm btn-outline-primary" title="Descargar"><i class="bi bi-download"></i></a>
                                <button class="btn btn-sm btn-outline-success" title="Restaurar este" onclick="restaurarLocal('{{ $f['nombre'] }}')"><i class="bi bi-arrow-counterclockwise"></i></button>
                                <form method="POST" action="{{ route('backups.eliminar', $f['nombre']) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este backup?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ============== RESTAURAR ============== --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100" style="border-top:4px solid #198754">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-success-subtle p-3 me-3">
                        <i class="bi bi-cloud-upload text-success fs-2"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Restaurar Copia de Seguridad</h5>
                        <small class="text-muted">Reemplaza los datos actuales con los del backup.</small>
                    </div>
                </div>

                <div class="alert alert-warning small mb-3">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>¡Atención!</strong> La restauración sobreescribe los datos actuales. Asegúrate de tener un backup reciente antes.
                </div>

                <form method="POST" action="{{ route('backups.restaurar') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="archivo_local" id="archivoLocal">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Archivo SQL desde tu equipo</label>
                        <input type="file" name="archivo" class="form-control" accept=".sql,.txt">
                        <div class="form-text">O selecciona un backup existente desde el panel izquierdo.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-danger">
                            Para confirmar, escribe: <code>RESTAURAR</code>
                        </label>
                        <input type="text" name="confirmacion" class="form-control" placeholder="RESTAURAR" required pattern="RESTAURAR">
                    </div>
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('¿Estás seguro de restaurar? Esta acción es irreversible.')">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar Ahora
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ============== RESET ============== --}}
    <div class="col-12">
        <div class="card shadow-sm" style="border-top:4px solid #dc3545">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-danger-subtle p-3 me-3">
                        <i class="bi bi-arrow-clockwise text-danger fs-2"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Resetear Sistema</h5>
                        <small class="text-muted">Limpia datos para empezar de cero o preparar el sistema para una nueva empresa.</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('backups.reset') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="card border h-100 text-center reset-option" data-nivel="transaccional" style="cursor:pointer">
                                <input type="radio" name="nivel" value="transaccional" class="d-none" required>
                                <div class="card-body">
                                    <i class="bi bi-receipt text-warning fs-1"></i>
                                    <h6 class="mt-2">Solo Transaccional</h6>
                                    <p class="small text-muted mb-0">
                                        Borra: <strong>pedidos, pagos, entregas, movimientos de stock</strong>.<br>
                                        <span class="text-success">Mantiene clientes, productos, repartidores, configuración.</span>
                                    </p>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="card border h-100 text-center reset-option" data-nivel="operativo" style="cursor:pointer">
                                <input type="radio" name="nivel" value="operativo" class="d-none" required>
                                <div class="card-body">
                                    <i class="bi bi-database-x text-warning fs-1"></i>
                                    <h6 class="mt-2">Operativo</h6>
                                    <p class="small text-muted mb-0">
                                        Borra: <strong>todo lo transaccional + clientes, productos, repartidores y cupones</strong>.<br>
                                        <span class="text-success">Mantiene categorías, zonas, configuración y usuarios.</span>
                                    </p>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="card border-danger h-100 text-center reset-option" data-nivel="total" style="cursor:pointer">
                                <input type="radio" name="nivel" value="total" class="d-none" required>
                                <div class="card-body">
                                    <i class="bi bi-fire text-danger fs-1"></i>
                                    <h6 class="mt-2 text-danger">Total / Nueva Empresa</h6>
                                    <p class="small text-muted mb-0">
                                        Borra: <strong>todo excepto usuarios y roles</strong>.<br>
                                        <span class="text-danger">Recrea categorías y configuración por defecto. Listo para empresa nueva.</span>
                                    </p>
                                </div>
                            </label>
                        </div>

                        <div class="col-md-9">
                            <label class="form-label small fw-semibold text-danger">
                                Para confirmar el reset, escribe: <code>RESET</code>
                            </label>
                            <input type="text" name="confirmacion" class="form-control" placeholder="RESET" required pattern="RESET">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Confirmas el reset? Esta acción NO se puede deshacer.')">
                                <i class="bi bi-trash3 me-1"></i>Ejecutar Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Selección visual del nivel de reset
document.querySelectorAll('.reset-option').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.reset-option').forEach(c => {
            c.classList.remove('border-primary','shadow-sm');
            c.style.background = '';
        });
        card.classList.add('border-primary','shadow-sm');
        card.style.background = '#e7f1ff';
        card.querySelector('input[type=radio]').checked = true;
    });
});

// Restaurar desde un backup local
function restaurarLocal(nombre) {
    if (!confirm('¿Restaurar el sistema con ' + nombre + '?\n\nEsto sobreescribira los datos actuales.')) return;
    document.getElementById('archivoLocal').value = nombre;
    // Pedir confirmación textual
    const conf = prompt('Para confirmar, escribe RESTAURAR');
    if (conf !== 'RESTAURAR') { alert('Restauración cancelada.'); return; }
    // Crear y enviar form
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = '{{ route("backups.restaurar") }}';
    f.innerHTML = `
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="archivo_local" value="${nombre}">
        <input type="hidden" name="confirmacion" value="RESTAURAR">
    `;
    document.body.appendChild(f);
    f.submit();
}
</script>
@endpush
@endsection
