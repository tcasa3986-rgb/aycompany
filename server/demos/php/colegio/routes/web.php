<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\ConceptoPagoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\GradoController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\SistemaController;

// ── Redirección raíz ──────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Autenticación (Laravel Breeze / Auth básico) ──────────────
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
    ->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->name('logout');

// ── Rutas protegidas ──────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Grados y Secciones
    Route::resource('grados', GradoController::class)->except(['create','show','edit']);
    Route::get('/grados/{grado}/secciones', [GradoController::class, 'secciones'])->name('grados.secciones');
    Route::post('/grados/{grado}/secciones', [GradoController::class, 'storeSeccion'])->name('grados.secciones.store');
    Route::put('/secciones/{seccion}', [GradoController::class, 'updateSeccion'])->name('secciones.update');
    Route::delete('/secciones/{seccion}', [GradoController::class, 'destroySeccion'])->name('secciones.destroy');

    // Materias y Asignaciones
    Route::get('/materias', [GradoController::class, 'materias'])->name('materias.index');
    Route::post('/materias', [GradoController::class, 'storeMateria'])->name('materias.store');
    Route::put('/materias/{materia}', [GradoController::class, 'updateMateria'])->name('materias.update');
    Route::delete('/materias/{materia}', [GradoController::class, 'destroyMateria'])->name('materias.destroy');
    Route::post('/asignaciones', [GradoController::class, 'storeAsignacion'])->name('asignaciones.store');
    Route::delete('/asignaciones/{asignacion}', [GradoController::class, 'destroyAsignacion'])->name('asignaciones.destroy');

    // Libro de Notas
    Route::get('/notas', [NotaController::class, 'index'])->name('notas.index');
    Route::post('/notas', [NotaController::class, 'guardar'])->name('notas.guardar');
    Route::get('/notas/boleta/{alumno}', [NotaController::class, 'boleta'])->name('notas.boleta');

    // Alumnos
    Route::resource('alumnos', AlumnoController::class);

    // Matrículas
    Route::resource('matriculas', MatriculaController::class);

    // Pagos
    Route::resource('pagos', PagoController::class);

    // Personal / Docentes
    Route::resource('personal', PersonalController::class);

    // Mensajes
    Route::resource('mensajes', MensajeController::class)->except(['edit', 'update']);

    // Conceptos de Pago
    Route::resource('conceptos', ConceptoPagoController::class)->except(['show']);
    Route::patch('conceptos/{concepto}/toggle', [ConceptoPagoController::class, 'toggleActivo'])->name('conceptos.toggle');

    // Configuración General
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

    // Mantenimiento del Sistema
    Route::get('/sistema', [SistemaController::class, 'index'])->name('sistema.index');
    Route::post('/sistema/backup', [SistemaController::class, 'backup'])->name('sistema.backup');
    Route::post('/sistema/restore', [SistemaController::class, 'restore'])->name('sistema.restore');
    Route::post('/sistema/reset', [SistemaController::class, 'reset'])->name('sistema.reset');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/pagos', [ReporteController::class, 'pagos'])->name('reportes.pagos');
    Route::get('/reportes/alumnos', [ReporteController::class, 'alumnos'])->name('reportes.alumnos');
    Route::get('/reportes/deudas', [ReporteController::class, 'deudas'])->name('reportes.deudas');
    Route::get('/reportes/exportar/{tipo}', [ReporteController::class, 'exportarCSV'])->name('reportes.exportar');

    // API interna: secciones por grado (para formularios dinámicos)
    Route::get('/api/grados/{grado}/secciones', function (\App\Models\Grado $grado) {
        return response()->json($grado->secciones);
    })->name('api.secciones');

    // API interna: monto de concepto de pago
    Route::get('/api/conceptos/{concepto}', function (\App\Models\ConceptoPago $concepto) {
        return response()->json($concepto);
    })->name('api.concepto');
});
