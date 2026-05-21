<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DoctorOfficeController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\DiagnosticTemplateController;
use App\Http\Controllers\InsuranceController;

Route::view('/', 'welcome');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Common AJAX Routes (Authenticad Users)
Route::middleware(['auth'])->group(function () {
    Route::get('available-slots', [DoctorScheduleController::class, 'getAvailableSlots'])
        ->name('doctors.available-slots');
    Route::get('specialties/{specialty}/doctors', [AppointmentController::class, 'getDoctorsBySpecialty'])
        ->name('specialties.doctors');

    // We can also let patients get appointment types if needed
    Route::get('doctors/{doctor}/appointment-types', function ($doctor) {
        $doctor = \App\Models\Doctor::findOrFail($doctor);
        return response()->json($doctor->appointmentTypes);
    })->name('doctors.appointment-types.json');
});

// Medical System Routes
Route::middleware(['auth', 'role:admin|doctor|receptionist'])->group(function () {

    // ── Users & Role Management (admin only) ─────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    });

    // Patients
    Route::get('patients/export/excel', [PatientController::class, 'exportExcel'])->name('patients.export.excel');
    Route::get('patients/export/pdf', [PatientController::class, 'exportPdf'])->name('patients.export.pdf');
    Route::get('patients/{patient}/export-profile', [PatientController::class, 'exportProfilePdf'])->name('patients.export-profile.pdf');
    Route::resource('patients', PatientController::class);

    // Waitlists
    Route::resource('waitlists', \App\Http\Controllers\WaitlistController::class)->except(['show']);

    // Specialties
    Route::resource('specialties', SpecialtyController::class)->except(['show']);

    // Doctors
    Route::resource('doctors', DoctorController::class);

    // Doctor Offices
    Route::resource('doctors.offices', DoctorOfficeController::class)->except(['show']);

    // Doctor Appointment Types
    Route::resource('doctors.appointment-types', \App\Http\Controllers\AppointmentTypeController::class)->except(['show']);

    // Doctor Schedules
    Route::prefix('doctors/{doctor}/schedule')->name('doctors.schedule.')->group(function () {
        Route::get('/', [DoctorScheduleController::class, 'index'])->name('index');
        Route::post('/', [DoctorScheduleController::class, 'store'])->name('store');
        Route::patch('/{schedule}/toggle', [DoctorScheduleController::class, 'toggle'])->name('toggle');
        Route::delete('/{schedule}', [DoctorScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/blocked', [DoctorScheduleController::class, 'storeBlocked'])->name('blocked.store');
        Route::delete('/blocked/{blocked}', [DoctorScheduleController::class, 'destroyBlocked'])->name('blocked.destroy');
    });


    // Appointments — rutas específicas ANTES del resource para evitar conflictos con {appointment}
    Route::get('appointments/calendar', [AppointmentController::class, 'calendar'])
        ->name('appointments.calendar');
    Route::get('appointments/calendar-events', [AppointmentController::class, 'calendarEvents'])
        ->name('appointments.calendarEvents');
    Route::resource('appointments', AppointmentController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
        ->name('appointments.updateStatus');
    Route::get('appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::patch('appointments/{appointment}/reschedule', [AppointmentController::class, 'doReschedule'])->name('appointments.doReschedule');


    // Historia Clínica Electrónica
    Route::get('patients/{patient}/medical-history', [\App\Http\Controllers\MedicalRecordController::class, 'patientHistory'])
        ->name('medical-records.patient-history');
    Route::resource('medical-records', \App\Http\Controllers\MedicalRecordController::class);

    // Plantillas de Diagnóstico
    Route::resource('diagnostic-templates', DiagnosticTemplateController::class)->except(['show']);

    // Attachments
    Route::delete('medical-records/attachments/{attachment}', [\App\Http\Controllers\MedicalRecordController::class, 'destroyAttachment'])
        ->name('medical-records.attachments.destroy');
    Route::get('medical-records/attachments/{attachment}/download', [\App\Http\Controllers\MedicalRecordController::class, 'downloadAttachment'])
        ->name('medical-records.attachments.download');

    // Recetas Médicas
    Route::get('prescriptions/{prescription}/pdf', [PrescriptionController::class, 'exportPdf'])->name('prescriptions.export.pdf');
    Route::resource('prescriptions', PrescriptionController::class);

    // Facturación
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
        ->name('invoices.pdf');
    Route::resource('invoices', InvoiceController::class);

    // Reportes y Estadísticas
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/appointments', [ReportController::class, 'appointments'])->name('appointments');
        Route::get('/appointments/pdf', [ReportController::class, 'appointmentsPdf'])->name('appointments.pdf');
        Route::get('/appointments/excel', [ReportController::class, 'appointmentsExcel'])->name('appointments.excel');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/revenue/pdf', [ReportController::class, 'revenuePdf'])->name('revenue.pdf');
        Route::get('/revenue/excel', [ReportController::class, 'revenueExcel'])->name('revenue.excel');
        Route::get('/patients', [ReportController::class, 'patients'])->name('patients');
    });

    // Configuración General
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::get('/settings/backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('settings.backups.index');
    Route::post('/settings/backups', [\App\Http\Controllers\BackupController::class, 'create'])->name('settings.backups.create');
    Route::post('/settings/backups/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('settings.backups.download');
    Route::get('/settings/email-templates', [\App\Http\Controllers\EmailTemplateController::class, 'index'])->name('settings.email_templates.index');
    Route::put('/settings/email-templates/{emailTemplate}', [\App\Http\Controllers\EmailTemplateController::class, 'update'])->name('settings.email_templates.update');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    // Seguros (Aseguradoras)
    Route::resource('insurances', InsuranceController::class)->except(['show']);

    // Chat (Médicos/Admins)
    Route::get('chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/{patient}', [\App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::post('chat/{patient}', [\App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');

    // Notifications
    Route::post('notifications/mark-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.mark-read');
});

// Patient Portal
Route::middleware(['auth', 'patient'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Portal\PatientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments', [\App\Http\Controllers\Portal\PatientPortalController::class, 'appointments'])->name('appointments');
    Route::get('/appointments/create', [\App\Http\Controllers\Portal\PatientPortalController::class, 'createAppointment'])->name('appointments.create');
    Route::post('/appointments', [\App\Http\Controllers\Portal\PatientPortalController::class, 'storeAppointment'])->name('appointments.store');
    Route::post('/appointments/{appointment}/cancel', [\App\Http\Controllers\Portal\PatientPortalController::class, 'cancelAppointment'])->name('appointments.cancel');
    Route::get('/medical-history', [\App\Http\Controllers\Portal\PatientPortalController::class, 'medicalHistory'])->name('medical-history');
    Route::get('/invoices', [\App\Http\Controllers\Portal\PatientPortalController::class, 'invoices'])->name('invoices');
    Route::post('/invoices/{invoice}/checkout', [\App\Http\Controllers\Portal\PaymentController::class, 'checkout'])->name('invoices.checkout');
    Route::get('/invoices/{invoice}/success', [\App\Http\Controllers\Portal\PaymentController::class, 'success'])->name('invoices.success');

    // Chat (Pacientes)
    Route::get('/chat', [\App\Http\Controllers\Portal\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{doctor}', [\App\Http\Controllers\Portal\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{doctor}', [\App\Http\Controllers\Portal\ChatController::class, 'store'])->name('chat.store');
});

// Stripe Webhooks (Cashier routes this by default, but we use our custom controller to handle our invoice logic)
Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook']);

require __DIR__ . '/auth.php';

