<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>


<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-700" style="font-weight:700;">Panel de Control</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            <?php echo e(now()->isoFormat('dddd, D [de] MMMM [de] YYYY')); ?>

        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('ventas.create')); ?>" class="btn btn-primary px-4">
            <i class="fas fa-plus me-2"></i>Nueva Venta
        </a>
        <a href="<?php echo e(route('reparaciones.create')); ?>" class="btn btn-outline-primary px-4">
            <i class="fas fa-tools me-2"></i>Nueva Reparación
        </a>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-purple">
            <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="kpi-value">S/ <?php echo e(number_format($ventasHoy, 0)); ?></div>
            <div class="kpi-label">Ventas de Hoy</div>
            <span class="kpi-badge">
                <i class="fas fa-calendar-day fa-xs"></i>
                S/ <?php echo e(number_format($ventasMes, 0)); ?> este mes
            </span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-pink">
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
            <div class="kpi-value"><?php echo e(number_format($totalClientes)); ?></div>
            <div class="kpi-label">Clientes Registrados</div>
            <span class="kpi-badge">
                <i class="fas fa-user-plus fa-xs"></i>
                +<?php echo e($clientesNuevosMes); ?> este mes
            </span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-cyan">
            <div class="kpi-icon"><i class="fas fa-box"></i></div>
            <div class="kpi-value"><?php echo e(number_format($totalProductos)); ?></div>
            <div class="kpi-label">Productos en Stock</div>
            <span class="kpi-badge">
                <?php if($stockBajo > 0): ?>
                    <i class="fas fa-exclamation-triangle fa-xs"></i>
                    <?php echo e($stockBajo); ?> con stock bajo
                <?php else: ?>
                    <i class="fas fa-check fa-xs"></i>
                    Stock óptimo
                <?php endif; ?>
            </span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-green">
            <div class="kpi-icon"><i class="fas fa-tools"></i></div>
            <div class="kpi-value"><?php echo e($reparacionesPendientes); ?></div>
            <div class="kpi-label">Reparaciones Activas</div>
            <span class="kpi-badge">
                <i class="fas fa-check-circle fa-xs"></i>
                <?php echo e($reparacionesListas); ?> listas para entregar
            </span>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
    
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="mb-0 fw-600" style="font-weight:600;">Ventas — Últimos 7 días</h6>
                        <span class="text-muted" style="font-size:12px;">Ingresos diarios en soles</span>
                    </div>
                    <?php if($crecimientoVentas >= 0): ?>
                        <span class="badge" style="background:#d1fae5; color:#065f46; font-size:12px; border-radius:20px; padding:5px 12px;">
                            <i class="fas fa-arrow-up fa-xs"></i> <?php echo e(number_format(abs($crecimientoVentas), 1)); ?>% vs mes anterior
                        </span>
                    <?php else: ?>
                        <span class="badge" style="background:#fee2e2; color:#991b1b; font-size:12px; border-radius:20px; padding:5px 12px;">
                            <i class="fas fa-arrow-down fa-xs"></i> <?php echo e(number_format(abs($crecimientoVentas), 1)); ?>% vs mes anterior
                        </span>
                    <?php endif; ?>
                </div>
                <canvas id="chartVentasDias" height="90"></canvas>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="mb-1 fw-600" style="font-weight:600;">Top Productos</h6>
                <p class="text-muted mb-3" style="font-size:12px;">Más vendidos este mes</p>

                <?php $__empty_1 = true; $__currentLoopData = $topProductos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:28px; height:28px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff;
                            background: <?php echo e(['linear-gradient(135deg,#a855f7,#7c3aed)', 'linear-gradient(135deg,#ec4899,#db2777)', 'linear-gradient(135deg,#06b6d4,#0284c7)', 'linear-gradient(135deg,#10b981,#059669)', 'linear-gradient(135deg,#f59e0b,#d97706)'][$i]); ?>;">
                            <?php echo e($i + 1); ?>

                        </div>
                        <div class="flex-1" style="min-width:0; flex:1;">
                            <div style="font-size:13px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <?php echo e($prod->nombre); ?>

                            </div>
                            <div style="font-size:11px; color:#9ca3af;"><?php echo e($prod->total_vendido); ?> unidades</div>
                        </div>
                        <div style="font-size:13px; font-weight:600; color:#1e1b4b; white-space:nowrap;">
                            S/ <?php echo e(number_format($prod->ingresos, 0)); ?>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted py-4" style="font-size:13px;">
                        <i class="fas fa-box-open fa-2x mb-2 d-block opacity-50"></i>
                        Sin ventas este mes
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="row g-3">
    
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-600" style="font-weight:600;">Últimas Ventas</h6>
                    <a href="<?php echo e(route('ventas.index')); ?>" class="btn btn-sm btn-outline-primary" style="font-size:12px;">
                        Ver todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>N° Venta</th>
                                <th>Cliente</th>
                                <th>Método</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $ultimasVentas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('ventas.show', $venta)); ?>"
                                       style="color:#a855f7; font-weight:500; font-size:13px; text-decoration:none;">
                                        <?php echo e($venta->numero_venta); ?>

                                    </a>
                                    <div style="font-size:11px; color:#9ca3af;">
                                        <?php echo e($venta->fecha_venta->diffForHumans()); ?>

                                    </div>
                                </td>
                                <td style="font-size:13px;">
                                    <?php echo e($venta->cliente->nombre_completo ?? '—'); ?>

                                </td>
                                <td>
                                    <span style="font-size:12px; text-transform:capitalize;">
                                        <?php echo e($venta->metodo_pago); ?>

                                    </span>
                                </td>
                                <td>
                                    <span style="font-size:13px; font-weight:600;">
                                        S/ <?php echo e(number_format($venta->total, 2)); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php
                                        $colores = ['completada'=>'success','pendiente'=>'warning','cancelada'=>'danger','devuelta'=>'secondary'];
                                        $color = $colores[$venta->estado] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo e($color); ?>-subtle text-<?php echo e($color); ?>"
                                          style="font-size:11px; border-radius:20px; padding:4px 10px;">
                                        <?php echo e(ucfirst($venta->estado)); ?>

                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4" style="font-size:13px;">
                                    <i class="fas fa-shopping-cart fa-2x mb-2 d-block opacity-40"></i>
                                    Sin ventas registradas
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-600" style="font-weight:600;">Reparaciones Recientes</h6>
                    <a href="<?php echo e(route('reparaciones.index')); ?>" class="btn btn-sm btn-outline-primary" style="font-size:12px;">
                        Ver todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <?php $__empty_1 = true; $__currentLoopData = $ultimasReparaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                    <?php
                        $bgEstado = [
                            'recibido'           => '#ede9fe',
                            'en_diagnostico'     => '#e0f2fe',
                            'esperando_repuesto' => '#fef9c3',
                            'en_reparacion'      => '#dbeafe',
                            'listo'              => '#d1fae5',
                            'entregado'          => '#f3f4f6',
                            'no_reparable'       => '#fee2e2',
                        ];
                        $icEstado = [
                            'recibido'           => 'fa-inbox',
                            'en_diagnostico'     => 'fa-search',
                            'esperando_repuesto' => 'fa-clock',
                            'en_reparacion'      => 'fa-wrench',
                            'listo'              => 'fa-check',
                            'entregado'          => 'fa-box',
                            'no_reparable'       => 'fa-times',
                        ];
                        $bg = $bgEstado[$rep->estado] ?? '#f3f4f6';
                        $ic = $icEstado[$rep->estado] ?? 'fa-tools';
                    ?>
                    <div style="width:36px; height:36px; border-radius:10px; background:<?php echo e($bg); ?>;
                                display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas <?php echo e($ic); ?>" style="font-size:14px; color:#6b7280;"></i>
                    </div>
                    <div class="flex-1" style="min-width:0;">
                        <div style="font-size:13px; font-weight:500;">
                            <?php echo e($rep->dispositivo); ?>

                            <?php if($rep->marca): ?> <span style="color:#9ca3af;">— <?php echo e($rep->marca); ?></span> <?php endif; ?>
                        </div>
                        <div style="font-size:11px; color:#9ca3af;">
                            <?php echo e($rep->cliente->nombre_completo ?? '—'); ?> · <?php echo e($rep->numero_orden); ?>

                        </div>
                    </div>
                    <a href="<?php echo e(route('reparaciones.show', $rep)); ?>"
                       class="btn btn-sm" style="font-size:11px; padding:3px 8px; border-radius:6px;
                       background:#f3f4f6; color:#374151; text-decoration:none;">
                        Ver
                    </a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center text-muted py-4" style="font-size:13px;">
                    <i class="fas fa-tools fa-2x mb-2 d-block opacity-40"></i>
                    Sin reparaciones registradas
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ── Gráfica de ventas por día ───────────────────────────────────────────
const diasLabels  = <?php echo json_encode($diasSemana->pluck('fecha'), 15, 512) ?>;
const diasTotales = <?php echo json_encode($diasSemana->pluck('total'), 15, 512) ?>;

const ctxDias = document.getElementById('chartVentasDias').getContext('2d');

const gradientFill = ctxDias.createLinearGradient(0, 0, 0, 200);
gradientFill.addColorStop(0, 'rgba(168, 85, 247, 0.3)');
gradientFill.addColorStop(1, 'rgba(236, 72, 153, 0.03)');

new Chart(ctxDias, {
    type: 'line',
    data: {
        labels: diasLabels,
        datasets: [{
            label: 'Ventas (S/)',
            data: diasTotales,
            borderColor: '#a855f7',
            backgroundColor: gradientFill,
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#a855f7',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e1b4b',
                titleFont: { family: 'Poppins', size: 12 },
                bodyFont: { family: 'Poppins', size: 13 },
                callbacks: {
                    label: ctx => ' S/ ' + ctx.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2})
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { family: 'Poppins', size: 11 }, color: '#9ca3af' }
            },
            y: {
                grid: { color: '#f3f4f6' },
                ticks: {
                    font: { family: 'Poppins', size: 11 },
                    color: '#9ca3af',
                    callback: v => 'S/ ' + v.toLocaleString('es-PE')
                }
            }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\CRMyERP\crm-gestion-tienda-celulares\resources\views/dashboard/index.blade.php ENDPATH**/ ?>