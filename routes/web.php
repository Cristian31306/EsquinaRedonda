<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CashShiftController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Página de Inicio (Landing Page)
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Páginas Legales (Cumplimiento Colombia)
Route::get('/politica-privacidad', function () {
    return Inertia::render('Legal/PrivacyPolicy');
})->name('legal.privacy');

Route::get('/terminos-condiciones', function () {
    return Inertia::render('Legal/Terms');
})->name('legal.terms');

// Rutas Autenticadas
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Administrativo
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión de Tickets (Operación)
    Route::group(['prefix' => 'tickets', 'as' => 'tickets.'], function () {
        Route::get('/entry', [TicketController::class, 'entry'])->name('entry');
        Route::post('/store', [TicketController::class, 'store'])->name('store');
        Route::get('/exit', [TicketController::class, 'exit'])->name('exit');
        Route::get('/search', [TicketController::class, 'search'])->name('search');
        Route::post('/{ticket}/pay', [TicketController::class, 'pay'])->middleware('shift_open')->name('pay');
    });

    // Control de Caja (Turnos)
    Route::group(['prefix' => 'shifts', 'as' => 'shifts.'], function () {
        Route::get('/', [CashShiftController::class, 'index'])->name('index');
        Route::get('/history', [CashShiftController::class, 'history'])->name('history');
        Route::post('/open', [CashShiftController::class, 'open'])->name('open');
        Route::post('/close', [CashShiftController::class, 'close'])->name('close');
        Route::get('/{cashShift}', [CashShiftController::class, 'show'])->name('show');
    });

    // Gestión de Tarifas (Administración)
    Route::post('rates/bulk', [RateController::class, 'storeBulk'])->name('rates.store_bulk');
    Route::delete('rates/category/{vehicle_type}', [RateController::class, 'destroyCategory'])->name('rates.destroy_category');
    Route::resource('rates', RateController::class);

    // Mensualidades
    Route::get('/memberships', [MembershipController::class, 'index'])->name('memberships.index');
    Route::post('/memberships', [MembershipController::class, 'store'])->middleware('shift_open')->name('memberships.store');
    Route::delete('/memberships/{membership}', [MembershipController::class, 'destroy'])->name('memberships.destroy');

    // Gestión de Usuarios (Administración avanzada)
    Route::get('/vehicles/check/{plate}', [VehicleController::class, 'check'])->name('vehicles.check');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

    // Configuración General
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Reportes (solo administradores)
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/export-pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('reports.pdf');

    // Gestión de Soporte Técnico (Tickets)
    Route::group(['prefix' => 'support', 'as' => 'support.'], function () {
        Route::get('/', [SupportController::class, 'index'])->name('index');
        Route::get('/create', [SupportController::class, 'create'])->name('create');
        Route::post('/', [SupportController::class, 'store'])->name('store');
        Route::get('/{ticket}', [SupportController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [SupportController::class, 'reply'])->name('reply');
        Route::patch('/{ticket}/close', [SupportController::class, 'close'])->name('close');
    });

    // Portal Super Admin (Algorah Control)
    Route::middleware([\App\Http\Middleware\EnsureUserIsSuperAdmin::class])->group(function () {
        Route::get('/system-admin', [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('admin.tenants.index');
        Route::post('/system-admin/tenants', [\App\Http\Controllers\SuperAdminController::class, 'storeTenant'])->name('admin.tenants.store');
        Route::patch('/system-admin/tenants/{tenant}', [\App\Http\Controllers\SuperAdminController::class, 'updateTenant'])->name('admin.tenants.update');
        Route::patch('/system-admin/tenants/{tenant}/toggle', [\App\Http\Controllers\SuperAdminController::class, 'toggleStatus'])->name('admin.tenants.toggle');
        
        // Gestión de Usuarios por Empresa
        Route::get('/system-admin/tenants/{tenant}/users', [\App\Http\Controllers\SuperAdminController::class, 'manageUsers'])->name('admin.tenants.users');
        Route::post('/system-admin/tenants/{tenant}/users', [\App\Http\Controllers\SuperAdminController::class, 'addUser'])->name('admin.tenants.users.store');
        Route::post('/system-admin/users/{user}/reset-password', [\App\Http\Controllers\SuperAdminController::class, 'resetUserPassword'])->name('admin.users.reset-password');
        Route::delete('/system-admin/users/{user}', [\App\Http\Controllers\SuperAdminController::class, 'deleteUser'])->name('admin.users.delete');
    });
});

require __DIR__.'/auth.php';
