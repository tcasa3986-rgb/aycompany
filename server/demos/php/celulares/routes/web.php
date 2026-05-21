<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReparacionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\BackupController;

// ── Autenticación ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/',       [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Rutas protegidas (requieren autenticación) ────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clientes
    Route::resource('clientes', ClienteController::class);

    // Productos
    Route::resource('productos', ProductoController::class);

    // Ventas
    Route::resource('ventas', VentaController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('/ventas/{venta}/cancelar', [VentaController::class, 'cancelar'])->name('ventas.cancelar');

    // Reparaciones
    Route::resource('reparaciones', ReparacionController::class)->except(['destroy']);

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    // Configuración
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion/usuarios', [ConfiguracionController::class, 'storeUsuario'])->name('configuracion.storeUsuario');
    Route::patch('/configuracion/usuarios/{usuario}/toggle', [ConfiguracionController::class, 'toggleUsuario'])->name('configuracion.toggleUsuario');
    Route::put('/configuracion/usuarios/{usuario}', [ConfiguracionController::class, 'updateUsuario'])->name('configuracion.updateUsuario');
    Route::delete('/configuracion/usuarios/{usuario}', [ConfiguracionController::class, 'destroyUsuario'])->name('configuracion.destroyUsuario');

    // Backup & Restauración
    Route::get('/backup',                       [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/crear',                [BackupController::class, 'crear'])->name('backup.crear');
    Route::get('/backup/descargar/{nombre}',    [BackupController::class, 'descargar'])->name('backup.descargar');
    Route::delete('/backup/eliminar/{nombre}',  [BackupController::class, 'eliminar'])->name('backup.eliminar');
    Route::post('/backup/restaurar',            [BackupController::class, 'restaurar'])->name('backup.restaurar');
    Route::post('/backup/resetear',             [BackupController::class, 'resetear'])->name('backup.resetear');

    // API interna para búsqueda de productos (para el formulario de ventas)
    Route::get('/api/productos/buscar', function () {
        $productos = \App\Models\Producto::with(['marca'])
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->when(request('q'), fn($q, $buscar) =>
                $q->where('nombre', 'like', "%$buscar%")
                  ->orWhere('codigo', 'like', "%$buscar%")
            )
            ->limit(10)
            ->get(['id', 'nombre', 'codigo', 'precio_venta', 'stock', 'marca_id']);

        return response()->json($productos);
    })->name('api.productos.buscar');

    // API interna para datos del dashboard (AJAX)
    Route::get('/api/dashboard/ventas-semana', function () {
        // Retorna datos de ventas por día para el gráfico
        $datos = \App\Models\Venta::select(
                \Illuminate\Support\Facades\DB::raw('DATE(fecha_venta) as fecha'),
                \Illuminate\Support\Facades\DB::raw('SUM(total) as total')
            )
            ->where('estado', 'completada')
            ->where('fecha_venta', '>=', \Carbon\Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return response()->json($datos);
    })->name('api.dashboard.ventas');

});
