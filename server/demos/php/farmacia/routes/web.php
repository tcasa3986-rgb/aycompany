<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\CuentaCorrienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PosInteractionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');

    Route::middleware('permission:inventario.view')->group(function () {
        Route::resource('productos', ProductoController::class)->except(['show']);
        Route::get('/productos/{producto}/barcode', [ProductoController::class, 'barcode'])->name('productos.barcode');
    });

    Route::middleware('permission:lotes.manage')->group(function () {
        Route::get('/productos/{producto}/lotes',           [LoteController::class, 'index'])->name('productos.lotes.index');
        Route::post('/productos/{producto}/lotes',          [LoteController::class, 'store'])->name('productos.lotes.store');
        Route::put('/productos/{producto}/lotes/{lote}',    [LoteController::class, 'update'])->name('productos.lotes.update');
        Route::delete('/productos/{producto}/lotes/{lote}', [LoteController::class, 'destroy'])->name('productos.lotes.destroy');
    });

    Route::resource('categorias', CategoriaController::class)->middleware('permission:categorias.manage')->except(['show']);
    Route::resource('proveedores', ProveedorController::class)->middleware('permission:proveedores.manage')->except(['show']);
    Route::resource('clientes', ClienteController::class)->middleware('permission:clientes.view')->except(['show']);
    
    Route::middleware('permission:clientes.manage')->group(function () {
        Route::get('/cuentas-clientes',             [CuentaCorrienteController::class, 'index'])->name('cuentas.index');
        Route::get('/cuentas-clientes/{cliente}',   [CuentaCorrienteController::class, 'show'])->name('cuentas.show');
        Route::post('/cuentas-clientes/{cliente}',  [CuentaCorrienteController::class, 'abono'])->name('cuentas.abono');
    });

    Route::middleware('permission:pos.use')->group(function () {
        Route::get('/pos',                [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/buscar',         [PosController::class, 'buscar'])->name('pos.buscar');
        Route::post('/pos',               [PosController::class, 'store'])->name('pos.store');
        Route::post('/pos/interacciones', [PosInteractionController::class, 'check'])->name('pos.interacciones');
    });

    Route::middleware('permission:pos.use|reportes.view')->group(function () {
        Route::get('/ventas',         [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
        Route::get('/ventas/{venta}/ticket', [VentaController::class, 'ticket'])->name('ventas.ticket');
    });

    Route::middleware('permission:caja.use')->group(function () {
        Route::get('/cajas',                    [CajaController::class, 'index'])->name('cajas.index');
        Route::post('/cajas/abrir',             [CajaController::class, 'abrir'])->name('cajas.abrir');
        Route::get('/cajas/{caja}',             [CajaController::class, 'show'])->name('cajas.show');
        Route::post('/cajas/{caja}/movimiento', [CajaController::class, 'movimiento'])->name('cajas.movimiento');
        Route::post('/cajas/{caja}/cerrar',     [CajaController::class, 'cerrar'])->name('cajas.cerrar');
    });

    Route::middleware('permission:compras.view')->group(function () {
        Route::get('/compras',          [CompraController::class, 'index'])->name('compras.index');
        Route::get('/compras/{compra}', [CompraController::class, 'show'])->name('compras.show');
    });

    Route::middleware('permission:compras.manage')->group(function () {
        Route::get('/compras-nueva',             [CompraController::class, 'create'])->name('compras.create');
        Route::post('/compras',                  [CompraController::class, 'store'])->name('compras.store');
        Route::post('/compras/{compra}/recibir', [CompraController::class, 'recibir'])->name('compras.recibir');
        Route::post('/compras/{compra}/anular',  [CompraController::class, 'anular'])->name('compras.anular');
    });

    Route::middleware('permission:recetas.view')->group(function () {
        Route::get('/recetas',          [RecetaController::class, 'index'])->name('recetas.index');
        Route::get('/recetas/{receta}', [RecetaController::class, 'show'])->name('recetas.show');
    });

    Route::middleware('permission:recetas.manage')->group(function () {
        Route::get('/recetas-nueva',       [RecetaController::class, 'create'])->name('recetas.create');
        Route::post('/recetas',            [RecetaController::class, 'store'])->name('recetas.store');
        Route::delete('/recetas/{receta}', [RecetaController::class, 'destroy'])->name('recetas.destroy');
    });

    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/settings/backup', [SettingController::class, 'backup'])->name('settings.backup');
        
        Route::get('/sucursales', [SucursalController::class, 'index'])->name('sucursales.index');
        Route::post('/sucursales', [SucursalController::class, 'store'])->name('sucursales.store');
    });

    Route::post('/sucursales/switch/{sucursal}', [SucursalController::class, 'switch'])->name('sucursales.switch');

    Route::middleware('permission:reportes.view')->group(function () {
        Route::get('/reportes',        [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
        Route::get('/reportes/top',    [ReporteController::class, 'topProductos'])->name('reportes.top');
        Route::get('/reportes/stock',  [ReporteController::class, 'stockCritico'])->name('reportes.stock');
        Route::get('/reportes/vencer', [ReporteController::class, 'porVencer'])->name('reportes.vencer');
    });

    // Agregar ruta de anulación de ventas (no estaba en el plan original)
    Route::post('/ventas/{venta}/anular', [VentaController::class, 'anular'])->middleware('permission:pos.use')->name('ventas.anular');
});
