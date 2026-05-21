<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\MuestraController;
use App\Http\Controllers\ResultadoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\MedicoReferidorController;
use App\Http\Controllers\ConvenioController;
use App\Http\Controllers\AreaLaboratorioController;
use App\Http\Controllers\ReactivoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConfiguracionController;

Route::get('/', fn() => redirect()->route('login'));

// Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ─── MÓDULO RECEPCIÓN ────────────────────────────────────────────────────
    Route::resource('pacientes', PacienteController::class);
    Route::resource('ordenes', OrdenController::class);
    Route::post('/ordenes/{orden}/entregar', [OrdenController::class, 'entregar'])->name('ordenes.entregar');

    // Citas
    Route::resource('citas', CitaController::class);
    Route::post('/citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado');

    // ─── MÓDULO LABORATORIO ──────────────────────────────────────────────────
    Route::get('/muestras', [MuestraController::class, 'index'])->name('muestras.index');
    Route::post('/muestras/{orden}/tomar', [MuestraController::class, 'tomarMuestra'])->name('muestras.tomar');
    Route::get('/muestras/{orden}/etiquetas', [MuestraController::class, 'imprimirEtiquetas'])->name('muestras.etiquetas');

    Route::get('/resultados', [ResultadoController::class, 'index'])->name('resultados.index');
    Route::get('/resultados/{orden}/create', [ResultadoController::class, 'create'])->name('resultados.create');
    Route::post('/resultados/{orden}', [ResultadoController::class, 'store'])->name('resultados.store');

    // Generación de PDF y envío de email
    Route::get('/resultados/{orden}/pdf', [ReporteController::class, 'resultadoPdf'])->name('resultados.pdf');
    Route::post('/resultados/{orden}/email', [ReporteController::class, 'enviarEmail'])->name('resultados.email');
    Route::get('/validar-resultado/{numero}', [ReporteController::class, 'validarResultado'])->name('resultados.validar');
    Route::get('/reportes/caja-diaria', [ReporteController::class, 'cajaDiariaPdf'])->name('reportes.caja_diaria');

    // ─── MÓDULO FACTURACIÓN ──────────────────────────────────────────────────
    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/facturas/{factura}', [FacturaController::class, 'show'])->name('facturas.show');
    Route::get('/facturas/cobrar', [FacturaController::class, 'create'])->name('facturas.create');
    Route::post('/facturas', [FacturaController::class, 'store'])->name('facturas.store');

    // ─── MÓDULO INVENTARIO ───────────────────────────────────────────────────
    Route::resource('reactivos', ReactivoController::class);
    Route::post('/reactivos/{reactivo}/stock', [ReactivoController::class, 'ajustarStock'])->name('reactivos.stock');

    // ─── MÓDULO ADMINISTRACIÓN ───────────────────────────────────────────────
    Route::resource('pruebas', PruebaController::class);
    Route::resource('medicos', MedicoReferidorController::class);
    Route::resource('convenios', ConvenioController::class);
    Route::resource('areas', AreaLaboratorioController::class);

    // Usuarios
    Route::resource('usuarios', UsuarioController::class);
    Route::post('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');

    // Configuración
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

    // Mantenimiento de Sistema (Backup, Restaurar, Reseteo)
    Route::get('/sistema/mantenimiento', [\App\Http\Controllers\SistemaController::class, 'mantenimiento'])->name('sistema.mantenimiento');
    Route::get('/sistema/manual', [\App\Http\Controllers\SistemaController::class, 'generarManualPdf'])->name('sistema.manual');
    Route::post('/sistema/backup', [\App\Http\Controllers\SistemaController::class, 'backup'])->name('sistema.backup');
    Route::post('/sistema/restore', [\App\Http\Controllers\SistemaController::class, 'restore'])->name('sistema.restore');
    Route::post('/sistema/reset', [\App\Http\Controllers\SistemaController::class, 'reset'])->name('sistema.reset');
});
