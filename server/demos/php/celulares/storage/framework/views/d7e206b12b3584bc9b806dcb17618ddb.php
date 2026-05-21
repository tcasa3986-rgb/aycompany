<?php $__env->startSection('title', 'Reportes'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Reportes</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Reportes y Estadísticas</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            Período: <strong><?php echo e($desde->format('d/m/Y')); ?></strong> al <strong><?php echo e($hasta->format('d/m/Y')); ?></strong>
        </p>
    </div>
    <button class="btn btn-outline-primary" onclick="window.print()">
        <i class="fas fa-file-pdf me-2"></i>Exportar PDF
    </button>
</div>


<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-1" style="font-size:12px;">Período rápido</label>
                <div class="d-flex gap-2">
                    <?php
                        $periodos = [
                            'hoy'       => ['Hoy',           now()->format('Y-m-d'), now()->format('Y-m-d')],
                            'semana'    => ['Esta semana',   now()->startOfWeek()->format('Y-m-d'), now()->format('Y-m-d')],
                            'mes'       => ['Este mes',      now()->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')],
                            'mes_ant'   => ['Mes anterior',  now()->subMonth()->startOfMonth()->format('Y-m-d'), now()->subMonth()->endOfMonth()->format('Y-m-d')],
                            'año'       => ['Este año',      now()->startOfYear()->format('Y-m-d'), now()->format('Y-m-d')],
                        ];
                    ?>
                    <?php $__currentLoopData = $periodos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$label, $d, $h]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('reportes.index', ['desde'=>$d, 'hasta'=>$h])); ?>"
                           class="btn btn-sm <?php echo e(request('desde')==$d && request('hasta')==$h ? 'btn-primary' : 'btn-outline-secondary'); ?>"
                           style="font-size:12px; border-radius:20px;">
                            <?php echo e($label); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1" style="font-size:12px;">Desde</label>
                <input type="date" class="form-control form-control-sm" name="desde"
                       value="<?php echo e(request('desde', $desde->format('Y-m-d'))); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1" style="font-size:12px;">Hasta</label>
                <input type="date" class="form-control form-control-sm" name="hasta"
                       value="<?php echo e(request('hasta', $hasta->format('Y-m-d'))); ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="fas fa-filter me-1"></i>Aplicar
                </button>
            </div>
        </form>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-purple">
            <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="kpi-value">S/ <?php echo e(number_format($totalVentas, 0)); ?></div>
            <div class="kpi-label">Total Ventas</div>
            <span class="kpi-badge"><i class="fas fa-receipt fa-xs"></i> <?php echo e($cantidadVentas); ?> transacciones</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-pink">
            <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
            <div class="kpi-value">S/ <?php echo e(number_format($ticketPromedio, 0)); ?></div>
            <div class="kpi-label">Ticket Promedio</div>
            <span class="kpi-badge"><i class="fas fa-shopping-bag fa-xs"></i> Por venta</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-cyan">
            <div class="kpi-icon"><i class="fas fa-tools"></i></div>
            <div class="kpi-value">S/ <?php echo e(number_format($totalReparaciones, 0)); ?></div>
            <div class="kpi-label">Ingresos Reparaciones</div>
            <span class="kpi-badge"><i class="fas fa-wrench fa-xs"></i> Servicio técnico</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card bg-grad-green">
            <div class="kpi-icon"><i class="fas fa-user-plus"></i></div>
            <div class="kpi-value"><?php echo e($clientesNuevos); ?></div>
            <div class="kpi-label">Clientes Nuevos</div>
            <span class="kpi-badge"><i class="fas fa-users fa-xs"></i> En el período</span>
        </div>
    </div>
</div>


<div class="row g-4 mb-4">
    
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">Ventas por Día</h6>
                <p class="text-muted mb-3" style="font-size:12px;">Ingresos diarios en el período seleccionado</p>
                <canvas id="chartDias" height="100"></canvas>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">Métodos de Pago</h6>
                <p class="text-muted mb-3" style="font-size:12px;">Distribución por forma de cobro</p>
                <canvas id="chartPago" height="180"></canvas>
                <div class="mt-3">
                    <?php $__currentLoopData = $ventasPorPago; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pago): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">
                        <span class="text-muted"><?php echo e(ucfirst($pago->metodo_pago)); ?></span>
                        <span class="fw-600">S/ <?php echo e(number_format($pago->monto, 2)); ?> (<?php echo e($pago->total); ?>)</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row g-4 mb-4">
    
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Top 10 Productos Más Vendidos</h6>
                <?php if($topProductos->count()): ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th style="width:30px;">#</th>
                                <th>Producto</th>
                                <th class="text-center">Unid.</th>
                                <th class="text-end">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $topProductos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div style="width:24px; height:24px; border-radius:6px; font-size:11px; font-weight:700; color:#fff; display:flex; align-items:center; justify-content:center;
                                        background:<?php echo e(['linear-gradient(135deg,#a855f7,#7c3aed)','linear-gradient(135deg,#ec4899,#db2777)','linear-gradient(135deg,#06b6d4,#0284c7)','linear-gradient(135deg,#10b981,#059669)','linear-gradient(135deg,#f59e0b,#d97706)'][$i] ?? '#6b7280'); ?>;">
                                        <?php echo e($i+1); ?>

                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:500;"><?php echo e($p->nombre); ?></div>
                                    <div style="font-size:11px; color:#9ca3af;"><?php echo e($p->codigo); ?></div>
                                </td>
                                <td class="text-center"><?php echo e($p->unidades); ?></td>
                                <td class="text-end fw-bold" style="color:#1e1b4b;">S/ <?php echo e(number_format($p->ingresos, 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted" style="font-size:13px;">Sin ventas en el período</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Top 10 Clientes</h6>
                <?php if($topClientes->count()): ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th style="width:30px;">#</th>
                                <th>Cliente</th>
                                <th class="text-center">Compras</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $topClientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div style="width:24px; height:24px; border-radius:6px; font-size:11px; font-weight:700; color:#fff; display:flex; align-items:center; justify-content:center;
                                        background:<?php echo e(['linear-gradient(135deg,#a855f7,#7c3aed)','linear-gradient(135deg,#ec4899,#db2777)','linear-gradient(135deg,#06b6d4,#0284c7)','linear-gradient(135deg,#10b981,#059669)','linear-gradient(135deg,#f59e0b,#d97706)'][$i] ?? '#6b7280'); ?>;">
                                        <?php echo e($i+1); ?>

                                    </div>
                                </td>
                                <td style="font-weight:500;"><?php echo e($c->nombre); ?></td>
                                <td class="text-center"><?php echo e($c->compras); ?></td>
                                <td class="text-end fw-bold" style="color:#1e1b4b;">S/ <?php echo e(number_format($c->total, 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted" style="font-size:13px;">Sin ventas en el período</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">⚠️ Productos con Stock Bajo</h6>
                    <span style="background:#fee2e2; color:#dc2626; border-radius:20px; padding:3px 12px; font-size:12px;">
                        <?php echo e($stockBajo->count()); ?> productos
                    </span>
                </div>
                <?php if($stockBajo->count()): ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Mínimo</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $stockBajo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div style="font-weight:500;"><?php echo e($p->nombre); ?></div>
                                    <div style="font-size:11px; color:#9ca3af;"><?php echo e($p->marca->nombre ?? ''); ?></div>
                                </td>
                                <td style="color:#6b7280;"><?php echo e($p->categoria->nombre ?? '—'); ?></td>
                                <td class="text-center">
                                    <span style="background:<?php echo e($p->stock<=0?'#fee2e2':'#fef3c7'); ?>; color:<?php echo e($p->stock<=0?'#dc2626':'#d97706'); ?>; border-radius:20px; padding:3px 10px; font-size:12px; font-weight:700;">
                                        <?php echo e($p->stock); ?>

                                    </span>
                                </td>
                                <td class="text-center" style="color:#9ca3af;"><?php echo e($p->stock_minimo); ?></td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('productos.edit', $p)); ?>"
                                       style="color:#a855f7; font-size:12px; text-decoration:none;">
                                        Reponer <i class="fas fa-arrow-right fa-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-success" style="font-size:13px;">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                    Todos los productos tienen stock óptimo
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Reparaciones por Estado</h6>
                <?php
                    $estCfg = ['recibido'=>['📥','#ede9fe','#6d28d9'],'en_diagnostico'=>['🔍','#e0f2fe','#0369a1'],'esperando_repuesto'=>['⏳','#fef9c3','#92400e'],'en_reparacion'=>['🔧','#dbeafe','#1d4ed8'],'listo'=>['✅','#d1fae5','#065f46'],'entregado'=>['📦','#f3f4f6','#374151'],'no_reparable'=>['❌','#fee2e2','#991b1b']];
                    $totalRep = $repPorEstado->sum('total');
                ?>
                <?php $__empty_1 = true; $__currentLoopData = $repPorEstado->sortByDesc('total'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $cfg = $estCfg[$rep->estado] ?? ['🔘','#f3f4f6','#374151']; $pct = $totalRep > 0 ? ($rep->total/$totalRep)*100 : 0; ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span style="font-size:13px;"><?php echo e($cfg[0]); ?> <?php echo e(str_replace('_',' ',ucfirst($rep->estado))); ?></span>
                        <span style="font-size:13px; font-weight:600; color:<?php echo e($cfg[2]); ?>;"><?php echo e($rep->total); ?></span>
                    </div>
                    <div class="progress" style="height:6px; border-radius:4px; background:#f3f4f6;">
                        <div class="progress-bar" style="width:<?php echo e($pct); ?>%; background:<?php echo e($cfg[2]); ?>;"></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-4 text-muted" style="font-size:13px;">Sin reparaciones en el período</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Gráfica ventas por día
const diasLabels  = <?php echo json_encode($ventasPorDia->pluck('fecha'), 15, 512) ?>;
const diasTotales = <?php echo json_encode($ventasPorDia->pluck('total'), 15, 512) ?>;

const ctxDias = document.getElementById('chartDias').getContext('2d');
const grad = ctxDias.createLinearGradient(0, 0, 0, 220);
grad.addColorStop(0, 'rgba(168,85,247,.3)');
grad.addColorStop(1, 'rgba(236,72,153,.02)');

new Chart(ctxDias, {
    type: 'bar',
    data: {
        labels: diasLabels,
        datasets: [
            {
                label: 'Ventas (S/)',
                data: diasTotales,
                backgroundColor: 'rgba(168,85,247,.75)',
                borderRadius: 6,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false },
            tooltip: { callbacks: { label: c => ' S/ ' + c.parsed.y.toLocaleString('es-PE', {minimumFractionDigits:2}) } }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 11 }, color: '#9ca3af' } },
            y: { grid: { color: '#f3f4f6' }, ticks: { font: { family: 'Poppins', size: 11 }, color: '#9ca3af', callback: v => 'S/ '+v.toLocaleString('es-PE') } }
        }
    }
});

// Gráfica métodos de pago
const pagoLabels = <?php echo json_encode($ventasPorPago->pluck('metodo_pago')->map(fn($p) => ucfirst($p)), 15, 512) ?>;
const pagoMontos = <?php echo json_encode($ventasPorPago->pluck('monto'), 15, 512) ?>;

new Chart(document.getElementById('chartPago'), {
    type: 'doughnut',
    data: {
        labels: pagoLabels,
        datasets: [{
            data: pagoMontos,
            backgroundColor: ['#a855f7','#ec4899','#06b6d4','#10b981'],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 11 }, padding: 12 } },
            tooltip: { callbacks: { label: c => ' S/ ' + c.parsed.toLocaleString('es-PE', {minimumFractionDigits:2}) } }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\CRMyERP\crm-gestion-tienda-celulares\resources\views/reportes/index.blade.php ENDPATH**/ ?>