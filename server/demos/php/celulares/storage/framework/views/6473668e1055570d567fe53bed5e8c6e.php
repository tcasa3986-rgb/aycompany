<?php $__env->startSection('title', 'Clientes'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Clientes</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Clientes</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Gestiona tu cartera de clientes</p>
    </div>
    <a href="<?php echo e(route('clientes.create')); ?>" class="btn btn-primary px-4">
        <i class="fas fa-plus me-2"></i>Nuevo Cliente
    </a>
</div>


<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                    <input type="text" class="form-control" name="buscar"
                           placeholder="Buscar por nombre, email, DNI, teléfono..."
                           value="<?php echo e(request('buscar')); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="tipo">
                    <option value="">Todos los tipos</option>
                    <option value="particular" <?php echo e(request('tipo')=='particular'?'selected':''); ?>>Particular</option>
                    <option value="empresa" <?php echo e(request('tipo')=='empresa'?'selected':''); ?>>Empresa</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>


<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Cliente</th>
                        <th>Contacto</th>
                        <th>Tipo</th>
                        <th>Ventas</th>
                        <th>Reparaciones</th>
                        <th>Registrado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-4" style="color:#9ca3af; font-size:12px;"><?php echo e($cliente->id); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:38px; height:38px; border-radius:50%;
                                    background: linear-gradient(135deg,#a855f7,#ec4899);
                                    display:flex; align-items:center; justify-content:center;
                                    color:#fff; font-weight:600; font-size:14px; flex-shrink:0;">
                                    <?php echo e(strtoupper(substr($cliente->nombre, 0, 1))); ?>

                                </div>
                                <div>
                                    <div style="font-weight:500; font-size:13.5px;"><?php echo e($cliente->nombre_completo); ?></div>
                                    <div style="font-size:11px; color:#9ca3af;"><?php echo e($cliente->email ?? 'Sin email'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;">
                            <div><?php echo e($cliente->telefono); ?></div>
                            <?php if($cliente->dni): ?>
                                <div style="font-size:11px; color:#9ca3af;">DNI: <?php echo e($cliente->dni); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?php echo e($cliente->tipo=='empresa'?'bg-info':'bg-secondary'); ?>"
                                  style="border-radius:20px; font-size:11px; padding:4px 10px;">
                                <?php echo e(ucfirst($cliente->tipo)); ?>

                            </span>
                        </td>
                        <td style="font-size:13px;">
                            <span class="badge bg-light text-dark" style="border-radius:20px; font-size:12px; padding:4px 10px;">
                                <?php echo e($cliente->ventas_count); ?>

                            </span>
                        </td>
                        <td style="font-size:13px;">
                            <span class="badge bg-light text-dark" style="border-radius:20px; font-size:12px; padding:4px 10px;">
                                <?php echo e($cliente->reparaciones_count); ?>

                            </span>
                        </td>
                        <td style="font-size:12px; color:#9ca3af;">
                            <?php echo e($cliente->created_at->format('d/m/Y')); ?>

                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="<?php echo e(route('clientes.show', $cliente)); ?>"
                                   class="btn btn-sm" style="background:#f3f4f6; color:#374151; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-eye fa-sm"></i>
                                </a>
                                <a href="<?php echo e(route('clientes.edit', $cliente)); ?>"
                                   class="btn btn-sm" style="background:#ede9fe; color:#7c3aed; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-edit fa-sm"></i>
                                </a>
                                <form action="<?php echo e(route('clientes.destroy', $cliente)); ?>" method="POST"
                                      onsubmit="return confirm('¿Eliminar cliente <?php echo e(addslashes($cliente->nombre_completo)); ?>?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm"
                                            style="background:#fee2e2; color:#dc2626; border-radius:8px; padding:5px 10px;">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-users fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                            <p class="text-muted mb-2">No hay clientes registrados</p>
                            <a href="<?php echo e(route('clientes.create')); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Agregar primer cliente
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($clientes->hasPages()): ?>
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:13px;">
                Mostrando <?php echo e($clientes->firstItem()); ?>–<?php echo e($clientes->lastItem()); ?> de <?php echo e($clientes->total()); ?> clientes
            </span>
            <?php echo e($clientes->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\CRMyERP\crm-gestion-tienda-celulares\resources\views/clientes/index.blade.php ENDPATH**/ ?>