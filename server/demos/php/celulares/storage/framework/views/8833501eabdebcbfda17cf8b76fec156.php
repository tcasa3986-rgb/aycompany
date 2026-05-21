<?php $__env->startSection('title', 'Configuración'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Configuración</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:12px;">
        <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px;">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- ── Header ── -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e1b4b;">Configuración del Sistema</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Gestión de usuarios y parámetros generales</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
        <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
    </button>
</div>

<div class="row g-4">

    <!-- ══════════ COLUMNA IZQUIERDA: Info del sistema ══════════ -->
    <div class="col-lg-4">

        <!-- Tarjeta de info de la tienda -->
        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#a855f7,#ec4899);
                                border-radius:14px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-store" style="color:#fff;font-size:22px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">CRM Celulares</h6>
                        <small class="text-muted">Sistema de Gestión</small>
                    </div>
                </div>

                <div style="font-size:13px;" class="mb-3">
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f3f4f6;">
                        <span class="text-muted">Versión</span>
                        <span class="fw-500">1.0.0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f3f4f6;">
                        <span class="text-muted">Framework</span>
                        <span class="fw-500">Laravel 10</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f3f4f6;">
                        <span class="text-muted">Base de datos</span>
                        <span class="fw-500">MySQL</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f3f4f6;">
                        <span class="text-muted">Zona horaria</span>
                        <span class="fw-500">America/Lima</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Moneda</span>
                        <span class="fw-500">Soles (S/)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Estadísticas del Sistema</h6>
                <?php
                    $stats = [
                        ['icon'=>'users','color'=>'#a855f7','label'=>'Usuarios activos','value'=>\App\Models\User::where('activo',true)->count()],
                        ['icon'=>'users','color'=>'#06b6d4','label'=>'Total clientes','value'=>\App\Models\Cliente::count()],
                        ['icon'=>'box','color'=>'#10b981','label'=>'Productos en inventario','value'=>\App\Models\Producto::where('activo',true)->count()],
                        ['icon'=>'shopping-cart','color'=>'#ec4899','label'=>'Ventas registradas','value'=>\App\Models\Venta::count()],
                        ['icon'=>'tools','color'=>'#f59e0b','label'=>'Órdenes de reparación','value'=>\App\Models\Reparacion::count()],
                    ];
                ?>
                <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid #f3f4f6; font-size:13px;">
                    <div style="width:32px;height:32px;background:<?php echo e($s['color']); ?>18;border-radius:8px;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-<?php echo e($s['icon']); ?>" style="color:<?php echo e($s['color']); ?>;font-size:13px;"></i>
                    </div>
                    <span class="text-muted flex-grow-1"><?php echo e($s['label']); ?></span>
                    <strong><?php echo e($s['value']); ?></strong>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Accesos Rápidos</h6>
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('productos.index')); ?>" class="btn btn-outline-secondary btn-sm text-start" style="border-radius:8px;">
                        <i class="fas fa-box me-2 text-muted"></i>Gestionar Inventario
                    </a>
                    <a href="<?php echo e(route('reportes.index')); ?>" class="btn btn-outline-secondary btn-sm text-start" style="border-radius:8px;">
                        <i class="fas fa-chart-bar me-2 text-muted"></i>Ver Reportes
                    </a>
                    <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-outline-secondary btn-sm text-start" style="border-radius:8px;">
                        <i class="fas fa-users me-2 text-muted"></i>Ver Clientes
                    </a>
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary btn-sm text-start" style="border-radius:8px;">
                        <i class="fas fa-th-large me-2 text-muted"></i>Ir al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════ COLUMNA DERECHA: Gestión de usuarios ══════════ -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="fw-bold mb-0">Gestión de Usuarios</h6>
                    <span style="background:#ede9fe;color:#7c3aed;border-radius:20px;padding:3px 12px;font-size:12px;">
                        <?php echo e($usuarios->count()); ?> usuarios
                    </span>
                </div>

                <!-- Leyenda de roles -->
                <div class="d-flex gap-3 mb-4" style="font-size:12px;">
                    <span><span style="display:inline-block;width:10px;height:10px;background:#a855f7;border-radius:50%;margin-right:4px;"></span>Admin</span>
                    <span><span style="display:inline-block;width:10px;height:10px;background:#06b6d4;border-radius:50%;margin-right:4px;"></span>Vendedor</span>
                    <span><span style="display:inline-block;width:10px;height:10px;background:#f59e0b;border-radius:50%;margin-right:4px;"></span>Técnico</span>
                </div>

                <div class="row g-3">
                    <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $rolColor = ['admin'=>'#a855f7','vendedor'=>'#06b6d4','tecnico'=>'#f59e0b'][$usuario->rol] ?? '#9ca3af';
                        $rolBg    = ['admin'=>'#ede9fe','vendedor'=>'#e0f2fe','tecnico'=>'#fef3c7'][$usuario->rol] ?? '#f3f4f6';
                        $rolTxt   = ['admin'=>'#7c3aed','vendedor'=>'#0369a1','tecnico'=>'#92400e'][$usuario->rol] ?? '#374151';
                        $inicial  = strtoupper(substr($usuario->name, 0, 1));
                    ?>
                    <div class="col-12">
                        <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                             style="background:#f9fafb;border:1px solid #f3f4f6;transition:all .2s;"
                             onmouseenter="this.style.borderColor='#e9d5ff'"
                             onmouseleave="this.style.borderColor='#f3f4f6'">

                            <!-- Avatar -->
                            <div style="width:44px;height:44px;background:<?php echo e($rolColor); ?>;border-radius:12px;
                                        display:flex;align-items:center;justify-content:center;
                                        color:#fff;font-weight:700;font-size:16px;flex-shrink:0;">
                                <?php echo e($inicial); ?>

                            </div>

                            <!-- Info -->
                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-600" style="font-size:14px;font-weight:600;"><?php echo e($usuario->name); ?></span>
                                    <span style="background:<?php echo e($rolBg); ?>;color:<?php echo e($rolTxt); ?>;
                                                 border-radius:20px;padding:2px 8px;font-size:11px;">
                                        <?php echo e(ucfirst($usuario->rol)); ?>

                                    </span>
                                    <?php if($usuario->id === auth()->id()): ?>
                                        <span style="background:#d1fae5;color:#065f46;border-radius:20px;padding:2px 8px;font-size:11px;">
                                            Tú
                                        </span>
                                    <?php endif; ?>
                                    <?php if(!$usuario->activo): ?>
                                        <span style="background:#fee2e2;color:#991b1b;border-radius:20px;padding:2px 8px;font-size:11px;">
                                            Inactivo
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:12px;color:#9ca3af;margin-top:2px;">
                                    <i class="fas fa-envelope me-1"></i><?php echo e($usuario->email); ?>

                                    <?php if($usuario->telefono): ?>
                                        &nbsp;·&nbsp;<i class="fas fa-phone me-1"></i><?php echo e($usuario->telefono); ?>

                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <!-- Editar -->
                                <button class="btn btn-sm btn-outline-secondary" style="border-radius:8px;padding:4px 10px;"
                                        title="Editar usuario"
                                        onclick="abrirModalEditar(<?php echo e($usuario->id); ?>, '<?php echo e(addslashes($usuario->name)); ?>', '<?php echo e($usuario->email); ?>', '<?php echo e($usuario->rol); ?>', '<?php echo e($usuario->telefono); ?>')">
                                    <i class="fas fa-edit" style="font-size:12px;"></i>
                                </button>

                                <?php if($usuario->id !== auth()->id()): ?>
                                <!-- Toggle activo -->
                                <form action="<?php echo e(route('configuracion.toggleUsuario', $usuario)); ?>" method="POST" style="display:inline;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-sm <?php echo e($usuario->activo ? 'btn-outline-warning' : 'btn-outline-success'); ?>"
                                            style="border-radius:8px;padding:4px 10px;"
                                            title="<?php echo e($usuario->activo ? 'Desactivar' : 'Activar'); ?> usuario">
                                        <i class="fas fa-<?php echo e($usuario->activo ? 'ban' : 'check'); ?>" style="font-size:12px;"></i>
                                    </button>
                                </form>

                                <!-- Eliminar -->
                                <form action="<?php echo e(route('configuracion.destroyUsuario', $usuario)); ?>" method="POST" style="display:inline;"
                                      onsubmit="return confirm('¿Eliminar al usuario <?php echo e(addslashes($usuario->name)); ?>? Esta acción no se puede deshacer.')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            style="border-radius:8px;padding:4px 10px;"
                                            title="Eliminar usuario">
                                        <i class="fas fa-trash" style="font-size:12px;"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <!-- Información de seguridad -->
        <div class="card mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-shield-alt me-2" style="color:#a855f7;"></i>Políticas de Acceso</h6>
                <div class="row g-3" style="font-size:13px;">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:#f8f5ff;border-left:3px solid #a855f7;">
                            <div class="fw-600 mb-1" style="font-weight:600;color:#7c3aed;">Admin</div>
                            <div class="text-muted" style="font-size:12px;">Acceso completo a todos los módulos, configuración y reportes.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:#e0f7fa;border-left:3px solid #06b6d4;">
                            <div class="fw-600 mb-1" style="font-weight:600;color:#0369a1;">Vendedor</div>
                            <div class="text-muted" style="font-size:12px;">Clientes, inventario, ventas y consulta de reportes.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:#fffbeb;border-left:3px solid #f59e0b;">
                            <div class="fw-600 mb-1" style="font-weight:600;color:#92400e;">Técnico</div>
                            <div class="text-muted" style="font-size:12px;">Gestión de reparaciones y consulta de inventario.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ MODAL: Nuevo Usuario ══════════ -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-user-plus me-2" style="color:#a855f7;"></i>Nuevo Usuario
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('configuracion.storeUsuario')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger" style="border-radius:10px;font-size:13px;">
                            <ul class="mb-0 ps-3">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>"
                                   placeholder="Ej: María García" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>"
                                   placeholder="usuario@tienda.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="nuevaPassword" class="form-control" required minlength="8">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('nuevaPassword','eyeNueva')">
                                    <i class="fas fa-eye" id="eyeNueva" style="font-size:13px;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('confirmPassword','eyeConfirm')">
                                    <i class="fas fa-eye" id="eyeConfirm" style="font-size:13px;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol <span class="text-danger">*</span></label>
                            <select name="rol" class="form-select" required>
                                <option value="">Seleccionar rol...</option>
                                <option value="admin"    <?php echo e(old('rol')=='admin'?'selected':''); ?>>👑 Administrador</option>
                                <option value="vendedor" <?php echo e(old('rol')=='vendedor'?'selected':''); ?>>🛒 Vendedor</option>
                                <option value="tecnico"  <?php echo e(old('rol')=='tecnico'?'selected':''); ?>>🔧 Técnico</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?php echo e(old('telefono')); ?>"
                                   placeholder="+51 999 999 999">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════ MODAL: Editar Usuario ══════════ -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:20px 24px;">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-user-edit me-2" style="color:#a855f7;"></i>Editar Usuario
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarUsuario" action="" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editNombre" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password" id="editPassword" class="form-control" minlength="8"
                                       placeholder="Dejar vacío para no cambiar">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('editPassword','eyeEdit')">
                                    <i class="fas fa-eye" id="eyeEdit" style="font-size:13px;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Repetir nueva contraseña">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol <span class="text-danger">*</span></label>
                            <select name="rol" id="editRol" class="form-select" required>
                                <option value="admin">👑 Administrador</option>
                                <option value="vendedor">🛒 Vendedor</option>
                                <option value="tecnico">🔧 Técnico</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="editTelefono" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function abrirModalEditar(id, nombre, email, rol, telefono) {
    document.getElementById('editNombre').value   = nombre;
    document.getElementById('editEmail').value    = email;
    document.getElementById('editRol').value      = rol;
    document.getElementById('editTelefono').value = telefono || '';
    document.getElementById('formEditarUsuario').action = '/configuracion/usuarios/' + id;
    var modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
    modal.show();
}

function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Auto-open modal si hay errores de validación (al crear)
<?php if($errors->any()): ?>
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalNuevoUsuario')).show();
    });
<?php endif; ?>
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\CRMyERP\crm-gestion-tienda-celulares\resources\views/configuracion/index.blade.php ENDPATH**/ ?>