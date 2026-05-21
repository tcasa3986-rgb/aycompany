<?php $__env->startSection('title', 'Ventas'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Ventas</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Ventas</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            Total del mes: <strong style="color:#a855f7;">S/ <?php echo e(number_format($totalMes, 2)); ?></strong>
        </p>
    </div>
    <a href="<?php echo e(route('ventas.create')); ?>" class="btn btn-primary px-4">
        <i class="fas fa-plus me-2"></i>Nueva Venta
    </a>
</div>


<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" class="form-control" name="buscar"
                       placeholder="N° venta o cliente..." value="<?php echo e(request('buscar')); ?>">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="estado">
                    <option value="">Todos los estados</option>
                    <option value="completada" <?php echo e(request('estado')=='completada'?'selected':''); ?>>Completada</option>
                    <option value="pendiente"  <?php echo e(request('estado')=='pendiente'?'selected':''); ?>>Pendiente</option>
                    <option value="cancelada"  <?php echo e(request('estado')=='cancelada'?'selected':''); ?>>Cancelada</option>
                    <option value="devuelta"   <?php echo e(request('estado')=='devuelta'?'selected':''); ?>>Devuelta</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="fecha_desde" value="<?php echo e(request('fecha_desde')); ?>">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="fecha_hasta" value="<?php echo e(request('fecha_hasta')); ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="<?php echo e(route('ventas.index')); ?>" class="btn btn-outline-secondary">
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
                        <th class="ps-4">N° Venta</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <span style="font-weight:600; color:#a855f7;"><?php echo e($venta->numero_venta); ?></span>
                        </td>
                        <td style="font-size:13px;">
                            <div><?php echo e($venta->cliente->nombre_completo ?? '—'); ?></div>
                            <div style="font-size:11px; color:#9ca3af;"><?php echo e($venta->cliente->telefono ?? ''); ?></div>
                        </td>
                        <td style="font-size:13px; color:#6b7280;"><?php echo e($venta->vendedor->name ?? '—'); ?></td>
                        <td style="font-size:12px;">
                            <div><?php echo e($venta->fecha_venta->format('d/m/Y')); ?></div>
                            <div style="color:#9ca3af;"><?php echo e($venta->fecha_venta->format('H:i')); ?></div>
                        </td>
                        <td>
                            <span style="font-size:12px;">
                                <?php
                                    $iconos = ['efectivo'=>'💵','tarjeta'=>'💳','transferencia'=>'🏦','cuotas'=>'📅'];
                                ?>
                                <?php echo e($iconos[$venta->metodo_pago] ?? ''); ?> <?php echo e(ucfirst($venta->metodo_pago)); ?>

                            </span>
                        </td>
                        <td style="font-weight:700; color:#1e1b4b;">
                            S/ <?php echo e(number_format($venta->total, 2)); ?>

                        </td>
                        <td>
                            <?php
                                $cfg = [
                                    'completada' => ['bg'=>'#d1fae5','color'=>'#065f46'],
                                    'pendiente'  => ['bg'=>'#fef3c7','color'=>'#92400e'],
                                    'cancelada'  => ['bg'=>'#fee2e2','color'=>'#991b1b'],
                                    'devuelta'   => ['bg'=>'#e5e7eb','color'=>'#374151'],
                                ];
                                $c = $cfg[$venta->estado] ?? ['bg'=>'#f3f4f6','color'=>'#374151'];
                            ?>
                            <span style="background:<?php echo e($c['bg']); ?>; color:<?php echo e($c['color']); ?>;
                                border-radius:20px; padding:4px 10px; font-size:11px; font-weight:500;">
                                <?php echo e(ucfirst($venta->estado)); ?>

                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="<?php echo e(route('ventas.show', $venta)); ?>"
                                   class="btn btn-sm" style="background:#ede9fe; color:#7c3aed; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-eye fa-sm"></i>
                                </a>
                                <?php if($venta->estado === 'completada'): ?>
                                <form action="<?php echo e(route('ventas.cancelar', $venta)); ?>" method="POST"
                                      onsubmit="return confirm('¿Cancelar esta venta?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-sm"
                                            style="background:#fee2e2; color:#dc2626; border-radius:8px; padding:5px 10px;">
                                        <i class="fas fa-ban fa-sm"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                            <p class="text-muted mb-2">No hay ventas registradas</p>
                            <a href="<?php echo e(route('ventas.create')); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Registrar primera venta
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($ventas->hasPages()): ?>
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:13px;">
                Mostrando <?php echo e($ventas->firstItem()); ?>–<?php echo e($ventas->lastItem()); ?> de <?php echo e($ventas->total()); ?> ventas
            </span>
            <?php echo e($ventas->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\CRMyERP\crm-gestion-tienda-celulares\resources\views/ventas/index.blade.php ENDPATH**/ ?>