<?php $__env->startSection('title', 'Reparaciones'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Reparaciones</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Servicio Técnico</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Órdenes de reparación y seguimiento</p>
    </div>
    <a href="<?php echo e(route('reparaciones.create')); ?>" class="btn btn-primary px-4">
        <i class="fas fa-plus me-2"></i>Nueva Orden
    </a>
</div>


<div class="row g-3 mb-4">
    <?php
        $estats = [
            ['label'=>'Recibidos', 'value'=>$estadisticas['recibidos'], 'icon'=>'fa-inbox', 'color'=>'#7c3aed', 'bg'=>'#ede9fe'],
            ['label'=>'En Proceso', 'value'=>$estadisticas['en_proceso'], 'icon'=>'fa-wrench', 'color'=>'#0284c7', 'bg'=>'#e0f2fe'],
            ['label'=>'Listos', 'value'=>$estadisticas['listos'], 'icon'=>'fa-check-circle', 'color'=>'#059669', 'bg'=>'#d1fae5'],
            ['label'=>'Entregados', 'value'=>$estadisticas['entregados'], 'icon'=>'fa-box', 'color'=>'#374151', 'bg'=>'#f3f4f6'],
        ];
    ?>
    <?php $__currentLoopData = $estats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div style="width:44px; height:44px; border-radius:12px; background:<?php echo e($e['bg']); ?>;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas <?php echo e($e['icon']); ?>" style="color:<?php echo e($e['color']); ?>; font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:22px; font-weight:700; color:<?php echo e($e['color']); ?>;"><?php echo e($e['value']); ?></div>
                    <div style="font-size:12px; color:#9ca3af;"><?php echo e($e['label']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" class="form-control" name="buscar"
                       placeholder="N° orden, dispositivo o cliente..." value="<?php echo e(request('buscar')); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="estado">
                    <option value="">Todos los estados</option>
                    <option value="recibido" <?php echo e(request('estado')=='recibido'?'selected':''); ?>>Recibido</option>
                    <option value="en_diagnostico" <?php echo e(request('estado')=='en_diagnostico'?'selected':''); ?>>En Diagnóstico</option>
                    <option value="esperando_repuesto" <?php echo e(request('estado')=='esperando_repuesto'?'selected':''); ?>>Esperando Repuesto</option>
                    <option value="en_reparacion" <?php echo e(request('estado')=='en_reparacion'?'selected':''); ?>>En Reparación</option>
                    <option value="listo" <?php echo e(request('estado')=='listo'?'selected':''); ?>>Listo</option>
                    <option value="entregado" <?php echo e(request('estado')=='entregado'?'selected':''); ?>>Entregado</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="prioridad">
                    <option value="">Toda prioridad</option>
                    <option value="urgente" <?php echo e(request('prioridad')=='urgente'?'selected':''); ?>>🔴 Urgente</option>
                    <option value="alta" <?php echo e(request('prioridad')=='alta'?'selected':''); ?>>🟠 Alta</option>
                    <option value="media" <?php echo e(request('prioridad')=='media'?'selected':''); ?>>🟡 Media</option>
                    <option value="baja" <?php echo e(request('prioridad')=='baja'?'selected':''); ?>>🟢 Baja</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="<?php echo e(route('reparaciones.index')); ?>" class="btn btn-outline-secondary">
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
                        <th class="ps-4">N° Orden</th>
                        <th>Dispositivo</th>
                        <th>Cliente</th>
                        <th>Técnico</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Presupuesto</th>
                        <th>Fecha</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $reparaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <span style="font-weight:600; color:#a855f7;"><?php echo e($rep->numero_orden); ?></span>
                        </td>
                        <td style="font-size:13px;">
                            <div style="font-weight:500;"><?php echo e($rep->dispositivo); ?></div>
                            <div style="font-size:11px; color:#9ca3af;">
                                <?php echo e($rep->marca); ?> <?php echo e($rep->modelo); ?>

                            </div>
                        </td>
                        <td style="font-size:13px;"><?php echo e($rep->cliente->nombre_completo ?? '—'); ?></td>
                        <td style="font-size:13px; color:#6b7280;"><?php echo e($rep->tecnico->name ?? '—'); ?></td>
                        <td>
                            <?php
                                $priCol = ['urgente'=>['🔴','#fee2e2','#991b1b'],'alta'=>['🟠','#ffedd5','#9a3412'],'media'=>['🟡','#fef9c3','#713f12'],'baja'=>['🟢','#d1fae5','#065f46']];
                                $pr = $priCol[$rep->prioridad] ?? ['⚪','#f3f4f6','#374151'];
                            ?>
                            <span style="background:<?php echo e($pr[1]); ?>; color:<?php echo e($pr[2]); ?>; border-radius:20px; padding:3px 9px; font-size:11px; font-weight:500;">
                                <?php echo e($pr[0]); ?> <?php echo e(ucfirst($rep->prioridad)); ?>

                            </span>
                        </td>
                        <td>
                            <?php
                                $stColors = [
                                    'recibido'           => ['#ede9fe','#6d28d9'],
                                    'en_diagnostico'     => ['#e0f2fe','#0369a1'],
                                    'esperando_repuesto' => ['#fef9c3','#92400e'],
                                    'en_reparacion'      => ['#dbeafe','#1d4ed8'],
                                    'listo'              => ['#d1fae5','#065f46'],
                                    'entregado'          => ['#f3f4f6','#374151'],
                                    'no_reparable'       => ['#fee2e2','#991b1b'],
                                ];
                                $sc = $stColors[$rep->estado] ?? ['#f3f4f6','#374151'];
                                $stLabel = str_replace('_',' ',ucfirst($rep->estado));
                            ?>
                            <span style="background:<?php echo e($sc[0]); ?>; color:<?php echo e($sc[1]); ?>; border-radius:20px; padding:4px 10px; font-size:11px; font-weight:500;">
                                <?php echo e($stLabel); ?>

                            </span>
                        </td>
                        <td style="font-size:13px; font-weight:600;">
                            <?php if($rep->presupuesto > 0): ?>
                                S/ <?php echo e(number_format($rep->presupuesto, 2)); ?>

                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;">
                            <div><?php echo e($rep->fecha_recepcion->format('d/m/Y')); ?></div>
                            <?php if($rep->fecha_estimada): ?>
                                <div style="color:#9ca3af; font-size:11px;">
                                    Est: <?php echo e($rep->fecha_estimada->format('d/m')); ?>

                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="<?php echo e(route('reparaciones.show', $rep)); ?>"
                                   class="btn btn-sm" style="background:#ede9fe; color:#7c3aed; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-eye fa-sm"></i>
                                </a>
                                <a href="<?php echo e(route('reparaciones.edit', $rep)); ?>"
                                   class="btn btn-sm" style="background:#e0f2fe; color:#0369a1; border-radius:8px; padding:5px 10px;">
                                    <i class="fas fa-edit fa-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-tools fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                            <p class="text-muted mb-2">No hay órdenes de reparación</p>
                            <a href="<?php echo e(route('reparaciones.create')); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Nueva Orden
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($reparaciones->hasPages()): ?>
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:13px;">
                Mostrando <?php echo e($reparaciones->firstItem()); ?>–<?php echo e($reparaciones->lastItem()); ?> de <?php echo e($reparaciones->total()); ?>

            </span>
            <?php echo e($reparaciones->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\CRMyERP\crm-gestion-tienda-celulares\resources\views/reparaciones/index.blade.php ENDPATH**/ ?>