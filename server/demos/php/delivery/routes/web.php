<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\RepartidorController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CuponController;
use App\Http\Controllers\RepartidorPanelController;
use App\Http\Controllers\BackupController;

// Redirect raíz al dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Rutas autenticadas
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('can:ver dashboard');

    // Clientes
    Route::middleware('can:ver clientes')->group(function () {
        Route::resource('clientes', ClienteController::class);
    });

    // Productos y Categorías
    Route::middleware('can:ver productos')->group(function () {
        Route::resource('productos', ProductoController::class);
        Route::resource('categorias', CategoriaController::class)->except(['create', 'edit', 'show']);

        // Inventario
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('/inventario/{producto}/kardex', [InventarioController::class, 'kardex'])->name('inventario.kardex');
        Route::post('/inventario/{producto}/ajustar', [InventarioController::class, 'ajustar'])->name('inventario.ajustar');
    });

    // Pedidos
    Route::middleware('can:ver pedidos')->group(function () {
        Route::resource('pedidos', PedidoController::class);
        Route::post('/pedidos/{pedido}/estado', [PedidoController::class, 'cambiarEstado'])
            ->name('pedidos.cambiar-estado')
            ->middleware('can:editar pedidos');

        // Comprobantes PDF
        Route::get('/pedidos/{pedido}/ticket', [ComprobanteController::class, 'ticket'])
            ->name('pedidos.ticket');
        Route::get('/pedidos/{pedido}/comprobante', [ComprobanteController::class, 'comprobante'])
            ->name('pedidos.comprobante');

        // Notificación WhatsApp/Email
        Route::get('/pedidos/{pedido}/notificar', [PedidoController::class, 'notificarWhatsapp'])
            ->name('pedidos.notificar');
    });

    // Reporte de ventas en PDF
    Route::middleware('can:ver reportes')->group(function () {
        Route::get('/reportes/ventas/pdf', [ComprobanteController::class, 'reporteVentas'])
            ->name('reportes.ventas.pdf');
    });

    // Repartidores
    Route::middleware('can:ver repartidores')->group(function () {
        Route::resource('repartidores', RepartidorController::class);
        Route::post('/repartidores/{repartidor}/estado', [RepartidorController::class, 'cambiarEstado'])
            ->name('repartidores.cambiar-estado');
    });

    // Entregas
    Route::middleware('can:ver entregas')->group(function () {
        Route::get('/entregas', [EntregaController::class, 'index'])->name('entregas.index');
        Route::post('/entregas/asignar', [EntregaController::class, 'asignar'])
            ->name('entregas.asignar')
            ->middleware('can:asignar entregas');
        Route::post('/entregas/{entrega}/estado', [EntregaController::class, 'actualizarEstado'])
            ->name('entregas.actualizar-estado')
            ->middleware('can:actualizar entregas');
    });

    // Pagos
    Route::middleware('can:ver pagos')->group(function () {
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::post('/pagos', [PagoController::class, 'store'])
            ->name('pagos.store')
            ->middleware('can:registrar pagos');
    });

    // Reportes
    Route::middleware('can:ver reportes')->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/ventas', [ReporteController::class, 'ventas'])->name('ventas');
        Route::get('/repartidores', [ReporteController::class, 'repartidores'])->name('repartidores');
        Route::get('/clientes', [ReporteController::class, 'clientes'])->name('clientes');
    });

    // Usuarios (solo admin/super-admin)
    Route::middleware('can:ver usuarios')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except(['show']);
    });

    // Cupones / promociones
    Route::middleware('can:editar pedidos')->group(function () {
        Route::get('/cupones', [CuponController::class, 'index'])->name('cupones.index');
        Route::post('/cupones', [CuponController::class, 'store'])->name('cupones.store');
        Route::put('/cupones/{cupon}', [CuponController::class, 'update'])->name('cupones.update');
        Route::delete('/cupones/{cupon}', [CuponController::class, 'destroy'])->name('cupones.destroy');
    });
    Route::post('/cupones/validar', [CuponController::class, 'validar_codigo'])->name('cupones.validar');

    // Zonas de delivery
    Route::middleware('can:ver configuracion')->group(function () {
        Route::get('/zonas', [ZonaController::class, 'index'])->name('zonas.index');
        Route::post('/zonas', [ZonaController::class, 'store'])->name('zonas.store');
        Route::put('/zonas/{zona}', [ZonaController::class, 'update'])->name('zonas.update');
        Route::delete('/zonas/{zona}', [ZonaController::class, 'destroy'])->name('zonas.destroy');
    });
    Route::get('/zonas/{zona}/tarifa', [ZonaController::class, 'tarifa'])->name('zonas.tarifa');

    // Configuración
    Route::middleware('can:ver configuracion')->group(function () {
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::put('/configuracion', [ConfiguracionController::class, 'update'])
            ->name('configuracion.update')
            ->middleware('can:editar configuracion');
    });

    // Backup / Restore / Reset (solo super-admin)
    Route::middleware('role:super-admin')->prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/', [BackupController::class, 'crear'])->name('crear');
        Route::get('/descargar/{archivo}', [BackupController::class, 'descargar'])->name('descargar');
        Route::delete('/{archivo}', [BackupController::class, 'eliminar'])->name('eliminar');
        Route::post('/restaurar', [BackupController::class, 'restaurar'])->name('restaurar');
        Route::post('/reset', [BackupController::class, 'reset'])->name('reset');
    });

    // Panel del repartidor (rol repartidor o admin)
    Route::prefix('repartidor')->name('repartidor.')->group(function () {
        Route::get('/', [RepartidorPanelController::class, 'index'])->name('index');
        Route::get('/entrega/{entrega}', [RepartidorPanelController::class, 'detalle'])->name('entrega');
        Route::post('/entrega/{entrega}/estado', [RepartidorPanelController::class, 'actualizarEstado'])->name('actualizar');
        Route::post('/ubicacion', [RepartidorPanelController::class, 'ubicacion'])->name('ubicacion');
    });

    // Perfil de usuario (Breeze)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
