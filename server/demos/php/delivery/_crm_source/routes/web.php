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
    });

    // Pedidos
    Route::middleware('can:ver pedidos')->group(function () {
        Route::resource('pedidos', PedidoController::class);
        Route::post('/pedidos/{pedido}/estado', [PedidoController::class, 'cambiarEstado'])
            ->name('pedidos.cambiar-estado')
            ->middleware('can:editar pedidos');
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

    // Configuración
    Route::middleware('can:ver configuracion')->group(function () {
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::put('/configuracion', [ConfiguracionController::class, 'update'])
            ->name('configuracion.update')
            ->middleware('can:editar configuracion');
    });

    // Perfil de usuario (Breeze)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
