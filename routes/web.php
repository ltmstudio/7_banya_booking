<?php

use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExtraServiceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SiteContentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\CabinetController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\BookingController as PublicBookingController;
use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Переключение языка (без auth)
Route::get('lang', [LanguageController::class, 'change'])->name('change.lang');

// Публичные маршруты
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/check-availability', [PublicBookingController::class, 'checkAvailability'])->name('public.availability');
Route::get('/api/busy-slots', [PublicBookingController::class, 'getBusySlots'])->name('public.busy-slots');
Route::get('/booking', [PublicBookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [PublicBookingController::class, 'store'])->name('public.booking.store');
Route::get('/booking/thank-you', fn () => Inertia::render('Booking/ThankYou'))->name('booking.thank-you');

// Личный кабинет клиента (отдельный guard: client)
Route::prefix('cabinet')->name('client.')->group(function () {
    Route::middleware('guest:client')->group(function () {
        Route::get('/login', [ClientAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [ClientAuthController::class, 'login'])->name('login.post');
        Route::get('/register', [ClientAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [ClientAuthController::class, 'register'])->name('register.post');
    });

    Route::post('/logout', [ClientAuthController::class, 'logout'])->middleware('auth:client')->name('logout');

    Route::middleware('client.auth')->group(function () {
        Route::get('/', [CabinetController::class, 'index'])->name('cabinet');
        Route::put('/profile', [CabinetController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [CabinetController::class, 'updatePassword'])->name('password.update');
    });
});

// Дашборд (auth, отдельно от /panel)
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Панель админки (auth)
Route::middleware('auth')->prefix('panel')->name('panel.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::patch('rooms/{room}/toggle-status', [AdminRoomController::class, 'toggleStatus'])->name('rooms.toggle-status');
    Route::delete('rooms/{room}/photos/{photo}', [AdminRoomController::class, 'destroyPhoto'])->name('rooms.photos.destroy');
    Route::resource('rooms', AdminRoomController::class);
    Route::resource('extra-services', ExtraServiceController::class)->parameters(['extra-services' => 'extraService']);
    Route::resource('clients', AdminClientController::class);
    Route::get('bookings/export', [AdminBookingController::class, 'export'])->name('bookings.export');
    Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.updateStatus');
    Route::patch('bookings/{booking}/final-amount', [AdminBookingController::class, 'setFinalAmount'])->name('bookings.setFinalAmount');
    Route::resource('bookings', AdminBookingController::class);
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('site-content', [SiteContentController::class, 'index'])->name('site-content.index');
    Route::put('site-content', [SiteContentController::class, 'update'])->name('site-content.update');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('audit-log', [AdminAuditLogController::class, 'index'])->name('audit-log.index');
    Route::resource('users', AdminUserController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
