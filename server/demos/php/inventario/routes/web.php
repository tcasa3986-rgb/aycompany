<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Resources
    Route::resource('equipos', App\Http\Controllers\EquipoController::class);
    Route::resource('empleados', App\Http\Controllers\EmpleadoController::class);
    Route::patch('/empleados/{empleado}/toggle', [App\Http\Controllers\EmpleadoController::class, 'toggleStatus'])->name('empleados.toggle');
    Route::get('/empleados/export/excel', [App\Http\Controllers\EmpleadoController::class, 'export'])->name('empleados.export');

    Route::resource('areas', App\Http\Controllers\AreaController::class);
    Route::patch('/areas/{area}/toggle', [App\Http\Controllers\AreaController::class, 'toggleStatus'])->name('areas.toggle');

    Route::resource('cargos', App\Http\Controllers\CargoController::class);
    Route::patch('/cargos/{cargo}/toggle', [App\Http\Controllers\CargoController::class, 'toggleStatus'])->name('cargos.toggle');

    Route::get('/asignaciones/{asignacion}/acta', [App\Http\Controllers\AsignacionController::class, 'downloadActa'])->name('asignaciones.acta');
    Route::post('/asignaciones/{asignacion}/upload-acta', [App\Http\Controllers\AsignacionController::class, 'uploadActa'])->name('asignaciones.upload-acta');
    Route::get('/asignaciones/{asignacion}/acta-devolucion', [App\Http\Controllers\AsignacionController::class, 'downloadActaDevolucion'])->name('asignaciones.acta-devolucion');
    Route::post('/asignaciones/{asignacion}/upload-acta-devolucion', [App\Http\Controllers\AsignacionController::class, 'uploadActaDevolucion'])->name('asignaciones.upload-acta-devolucion');
    Route::patch('/asignaciones/{asignacion}/annul', [App\Http\Controllers\AsignacionController::class, 'annul'])->name('asignaciones.annul');
    Route::patch('/asignaciones/{asignacion}/return', [App\Http\Controllers\AsignacionController::class, 'return'])->name('asignaciones.return');
    Route::resource('asignaciones', App\Http\Controllers\AsignacionController::class);
    Route::resource('sucursales', App\Http\Controllers\SucursalController::class);
    Route::patch('/sucursales/{sucursale}/toggle', [App\Http\Controllers\SucursalController::class, 'toggleStatus'])->name('sucursales.toggle');
    Route::resource('marcas', App\Http\Controllers\MarcaController::class);
    Route::patch('/marcas/{marca}/toggle', [App\Http\Controllers\MarcaController::class, 'toggleStatus'])->name('marcas.toggle');
    Route::resource('modelos', App\Http\Controllers\ModeloController::class);
    Route::patch('/modelos/{modelo}/toggle', [App\Http\Controllers\ModeloController::class, 'toggleStatus'])->name('modelos.toggle');
    Route::resource('tipos-equipo', App\Http\Controllers\TipoEquipoController::class);
    Route::patch('/tipos-equipo/{tiposEquipo}/toggle', [App\Http\Controllers\TipoEquipoController::class, 'toggleStatus'])->name('tipos-equipo.toggle');
    Route::resource('reparaciones', App\Http\Controllers\ReparacionController::class);
    Route::resource('bajas', App\Http\Controllers\BajaController::class);

    // Reportes PDF
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [App\Http\Controllers\ReporteController::class, 'index'])->name('index');
        Route::get('/equipos', [App\Http\Controllers\ReporteController::class, 'equipos'])->name('equipos');
        Route::get('/asignaciones', [App\Http\Controllers\ReporteController::class, 'asignaciones'])->name('asignaciones');
        Route::get('/empleado/{id}', [App\Http\Controllers\ReporteController::class, 'empleado'])->name('empleado');
        Route::get('/reparaciones', [App\Http\Controllers\ReporteController::class, 'reparaciones'])->name('reparaciones');
        Route::get('/bajas', [App\Http\Controllers\ReporteController::class, 'bajas'])->name('bajas');
    });

    // Admin Panel (solo administradores)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::patch('/users/{user}/toggle', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::resource('roles', App\Http\Controllers\RoleController::class);

        // Configuración
        Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    });


    // Búsqueda Global
    Route::get('/search', [App\Http\Controllers\SearchController::class, 'search'])->name('search');

    // Export to Excel
    Route::resource('equipos', EquipoController::class);
    Route::patch('/equipos/{equipo}/toggle', [EquipoController::class, 'toggleStatus'])->name('equipos.toggle');
    Route::get('/equipos/export/excel', [EquipoController::class, 'export'])->name('equipos.export');

    Route::get('/asignaciones/export', [App\Http\Controllers\AsignacionController::class, 'export'])->name('asignaciones.export');

    // API endpoints for dependent dropdowns
    Route::get('/api/modelos/{marca}', [App\Http\Controllers\EquipoController::class, 'getModelos'])->name('api.modelos');
    Route::get('/api/areas/{area}/cargos', [App\Http\Controllers\AreaController::class, 'getCargos'])->name('api.cargos');
});

require __DIR__ . '/auth.php';
