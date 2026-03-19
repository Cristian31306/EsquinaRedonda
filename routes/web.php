<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CashShiftController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirección inicial al Login
Route::get('/', function () {
    return redirect()->route('login');
});

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
        Route::post('/{ticket}/pay', [TicketController::class, 'pay'])->name('pay');
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
    Route::post('/memberships', [MembershipController::class, 'store'])->name('memberships.store');
    Route::delete('/memberships/{membership}', [MembershipController::class, 'destroy'])->name('memberships.destroy');

    // Gestión de Usuarios (Administración avanzada)
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

    // Configuración General
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
