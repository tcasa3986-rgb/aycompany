<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-dump', function() {
    try {
        $sys = new \App\Http\Controllers\SystemController();
        $res = $sys->downloadBackup();
        if ($res instanceof \Illuminate\Http\RedirectResponse) {
            return "Failed: It returned a redirect response. Meaning it threw an exception inside.";
        }
        return response($res->getContent(), 200, ['Content-Type' => 'text/plain']);
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────
    Route::get('/dashboard', function () {
        $totalQuotations  = \App\Models\Quotation::count();
        $totalClients     = \App\Models\Client::count();
        $totalProducts    = \App\Models\Product::count();
        $defCurrency      = \App\Models\Setting::get('default_currency', 'PEN');
        $totalAmount      = \App\Models\Quotation::where('currency', $defCurrency)->sum('total');
        $byStatus         = \App\Models\Quotation::selectRaw('status, count(*) as total')
                                ->groupBy('status')->pluck('total','status');
        $recentQuotations = \App\Models\Quotation::with('client')->latest()->take(5)->get();

        // Monthly data for last 6 months
        $monthlyData   = [];
        $monthLabels   = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $monthLabels[] = $d->translatedFormat('M Y');
            $monthlyData[] = (float) \App\Models\Quotation::whereYear('issue_date', $d->year)
                ->whereMonth('issue_date', $d->month)->sum('total');
        }

        return view('dashboard', compact(
            'totalQuotations','totalClients','totalProducts',
            'totalAmount','byStatus','recentQuotations',
            'monthlyData','monthLabels'
        ));
    })->name('dashboard');

    // ── Clientes ───────────────────────────────────────────
    Route::get('/clients/export/excel', [ClientController::class, 'exportExcel'])->name('clients.export.excel');
    Route::get('/clients/export/pdf',   [ClientController::class, 'exportPdf'])->name('clients.export.pdf');
    Route::resource('clients', ClientController::class);

    // ── Productos ──────────────────────────────────────────
    Route::get('/products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
    Route::get('/products/export/pdf',   [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    Route::resource('products', ProductController::class)->except(['show']);

    // ── Empresas ───────────────────────────────────────────
    Route::get('/companies/export/excel', [CompanyController::class, 'exportExcel'])->name('companies.export.excel');
    Route::get('/companies/export/pdf',   [CompanyController::class, 'exportPdf'])->name('companies.export.pdf');
    Route::resource('companies', CompanyController::class)->except(['show']);

    // ── Cotizaciones ───────────────────────────────────────
    Route::get('/quotations/export/excel', [QuotationController::class, 'exportExcel'])->name('quotations.export.excel');
    Route::get('/quotations/export/pdf',   [QuotationController::class, 'exportPdf'])->name('quotations.export.pdf');
    Route::resource('quotations', QuotationController::class);
    Route::patch('/quotations/{quotation}/status',  [QuotationController::class, 'updateStatus'])->name('quotations.status');
    Route::post('/quotations/{quotation}/clone',    [QuotationController::class, 'clone'])->name('quotations.clone');
    Route::get('/quotations/{quotation}/pdf',       [QuotationController::class, 'pdf'])->name('quotations.pdf');
    Route::get('/quotations/{quotation}/preview',   [QuotationController::class, 'preview'])->name('quotations.preview');
    Route::post('/quotations/{quotation}/email',    [QuotationController::class, 'sendEmail'])->name('quotations.email');

    // ── Reportes ───────────────────────────────────────────
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf',   [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // ── Configuración ──────────────────────────────────────
    Route::get('/settings',  [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-email', [SettingController::class, 'testEmail'])->name('settings.test-email');

    // ── Perfil ─────────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Mantenimiento del Sistema ──────────────────────────
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/backup', [\App\Http\Controllers\SystemController::class, 'index'])->name('backup');
        Route::get('/backup/download', [\App\Http\Controllers\SystemController::class, 'downloadBackup'])->name('backup.download');
        Route::post('/backup/restore', [\App\Http\Controllers\SystemController::class, 'restoreBackup'])->name('backup.restore');
        Route::post('/reset', [\App\Http\Controllers\SystemController::class, 'resetSystem'])->name('reset');
    });
});

require __DIR__ . '/auth.php';
