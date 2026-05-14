<?php

use App\Http\Controllers\HotelPanel\HotelAdminPinController;
use App\Http\Controllers\HotelPanel\HotelAdminPinSettingsController;
use App\Http\Controllers\HotelPanel\HotelDashboardController;
use App\Http\Controllers\HotelPanel\HotelPinController;
use App\Http\Controllers\HotelPanel\HotelPinResetRequestController;
use App\Http\Controllers\HotelPanel\HotelPinSettingsController;
use App\Http\Controllers\HotelPanel\HotelQrCreationRequestController;
use App\Http\Controllers\HotelPanel\HotelQrPointController;
use App\Http\Controllers\HotelPanel\HotelReportController;
use App\Http\Controllers\HotelPanel\HotelRequestHistoryController;
use App\Http\Controllers\HotelPanel\HotelRequestStatusController;
use App\Http\Controllers\PublicHotelAccessController;
use App\Http\Controllers\PublicQrRequestController;
use App\Http\Controllers\SysApp\SysAppAuditLogController;
use App\Http\Controllers\SysApp\SysAppAuthController;
use App\Http\Controllers\SysApp\SysAppHotelController;
use App\Http\Controllers\SysApp\SysAppPinResetRequestController;
use App\Http\Controllers\SysApp\SysAppQrCreationRequestController;
use App\Http\Controllers\SysApp\SysAppQrPointController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelPanel\HotelLicenseController;
use App\Http\Controllers\HotelPanel\HotelDocumentationController;
use App\Http\Controllers\HotelPanel\HotelTrialTermsController;


/*
|--------------------------------------------------------------------------
| Acceso público general
|--------------------------------------------------------------------------
|
| Entrada pública para buscar/acceder al hotel mediante PIN.
|
*/

Route::get('/', function () {
    return redirect()->route('public.access.create');
})->name('public.access.home');

Route::get('/acceso', [PublicHotelAccessController::class, 'create'])
    ->name('public.access.create');

Route::post('/acceso', [PublicHotelAccessController::class, 'store'])
    ->name('public.access.store');

/*
|--------------------------------------------------------------------------
| QR público del huésped
|--------------------------------------------------------------------------
|
| El middleware hotel.license valida que el hotel tenga prueba/licencia activa.
| El throttle limita abuso de solicitudes públicas.
|
*/

Route::get('/r/{code}', [PublicQrRequestController::class, 'show'])
    ->middleware('hotel.license')
    ->name('public.qr.show');

Route::post('/r/{code}', [PublicQrRequestController::class, 'store'])
    ->middleware(['hotel.license', 'throttle:hoteldesk-public-qr-request'])
    ->name('public.qr.store');

/*
|--------------------------------------------------------------------------
| Panel del hotel
|--------------------------------------------------------------------------
|
| Rutas por slug del hotel:
| /h/{hotel}
|
| El login y recuperación de PIN quedan libres.
| El dashboard, solicitudes, reportes y administración interna requieren:
| - PIN de recepción: hotel.pin
| - Licencia activa: hotel.license
| - PIN admin del hotel para módulos sensibles: hotel.admin.pin
|
*/

Route::prefix('h/{hotel:slug}')
    ->name('hotel.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Acceso por PIN de recepción
        |--------------------------------------------------------------------------
        */

        Route::get('/', [HotelPinController::class, 'showLogin'])
            ->name('login');

        Route::post('/pin', [HotelPinController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('pin.login');

        /*
        |--------------------------------------------------------------------------
        | Recuperación de PIN sin sesión
        |--------------------------------------------------------------------------
        */

        Route::get('/pin-reset', [HotelPinResetRequestController::class, 'create'])
            ->name('pin-reset.create');

        Route::post('/pin-reset', [HotelPinResetRequestController::class, 'store'])
            ->middleware('throttle:3,10')
            ->name('pin-reset.store');
/*
|--------------------------------------------------------------------------
| Panel con sesión por PIN de recepción
|--------------------------------------------------------------------------
|
| La sección de licencia queda visible aunque el trial esté vencido.
| La operación normal sí requiere licencia activa.
|
*/

Route::middleware('hotel.pin')->group(function () {
    Route::post('/logout', [HotelPinController::class, 'logout'])
        ->name('logout');

    Route::get('/license', [HotelLicenseController::class, 'index'])
        ->name('license.index');

        Route::get('/documentation', [HotelDocumentationController::class, 'index'])
    ->name('docs.index');


    Route::get('/terms', [HotelTrialTermsController::class, 'show'])
        ->name('terms.show');

    Route::post('/terms/accept', [HotelTrialTermsController::class, 'accept'])
        ->middleware('throttle:5,1')
        ->name('terms.accept');

    /*
    |--------------------------------------------------------------------------
    | Panel operativo protegido por licencia activa
    |--------------------------------------------------------------------------
    */

    Route::middleware('hotel.license')->group(function () {
        Route::get('/dashboard', [HotelDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/requests/feed', [HotelDashboardController::class, 'feed'])
            ->name('requests.feed');

        Route::patch('/requests/{request}/take', [HotelRequestStatusController::class, 'take'])
            ->name('requests.take');

        Route::patch('/requests/{request}/complete', [HotelRequestStatusController::class, 'complete'])
            ->name('requests.complete');

        Route::patch('/requests/{request}/cancel', [HotelRequestStatusController::class, 'cancel'])
            ->name('requests.cancel');

        Route::get('/requests/history', [HotelRequestHistoryController::class, 'index'])
            ->name('requests.history');

        Route::get('/settings/pin', [HotelPinSettingsController::class, 'edit'])
            ->name('settings.pin.edit');

        Route::put('/settings/pin', [HotelPinSettingsController::class, 'update'])
            ->name('settings.pin.update');

        Route::get('/admin-pin', [HotelAdminPinController::class, 'show'])
            ->name('admin-pin.show');

        Route::post('/admin-pin', [HotelAdminPinController::class, 'verify'])
            ->middleware('throttle:5,1')
            ->name('admin-pin.verify');

        Route::post('/admin-pin/logout', [HotelAdminPinController::class, 'logout'])
            ->name('admin-pin.logout');

        Route::middleware('hotel.admin.pin')->group(function () {
            Route::get('/reports', [HotelReportController::class, 'index'])
                ->name('reports.index');

            Route::get('/qr-points', [HotelQrPointController::class, 'index'])
                ->name('qr-points.index');

            Route::get('/qr-points/{point}/print', [HotelQrPointController::class, 'print'])
                ->name('qr-points.print');

            Route::patch('/qr-points/{point}/invalidate', [HotelQrPointController::class, 'invalidate'])
                ->name('qr-points.invalidate');

            Route::patch('/qr-points/{point}/regenerate', [HotelQrPointController::class, 'regenerate'])
                ->name('qr-points.regenerate');

            Route::get('/qr-requests', [HotelQrCreationRequestController::class, 'index'])
                ->name('qr-requests.index');

            Route::get('/qr-requests/create', [HotelQrCreationRequestController::class, 'create'])
                ->name('qr-requests.create');

            Route::post('/qr-requests', [HotelQrCreationRequestController::class, 'store'])
                ->middleware('throttle:10,10')
                ->name('qr-requests.store');

            Route::get('/admin-pin/settings', [HotelAdminPinSettingsController::class, 'edit'])
                ->name('admin-pin.settings.edit');

            Route::put('/admin-pin/settings', [HotelAdminPinSettingsController::class, 'update'])
                ->name('admin-pin.settings.update');
        });
    });
}); // Fin grupo hotel.pin

    }); // Fin grupo h/{hotel:slug}

/*
|--------------------------------------------------------------------------
| Admin interno SysApp
|--------------------------------------------------------------------------
*/

Route::prefix('sysapp')
    ->name('sysapp.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Login SysApp
        |--------------------------------------------------------------------------
        */

        Route::get('/', [SysAppAuthController::class, 'login'])
            ->name('login');

        Route::get('/login', [SysAppAuthController::class, 'login'])
            ->name('login.form');

        Route::post('/login', [SysAppAuthController::class, 'authenticate'])
            ->middleware('throttle:5,1')
            ->name('login.post');

        Route::post('/logout', [SysAppAuthController::class, 'logout'])
            ->name('logout');

        /*
        |--------------------------------------------------------------------------
        | Panel SysApp protegido
        |--------------------------------------------------------------------------
        */

        Route::middleware('sysapp.auth')->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            Route::get('/audit-logs', [SysAppAuditLogController::class, 'index'])
                ->name('audit-logs.index');

            /*
            |--------------------------------------------------------------------------
            | Hoteles
            |--------------------------------------------------------------------------
            */

            Route::get('/hotels', [SysAppHotelController::class, 'index'])
                ->name('hotels.index');

            Route::get('/hotels/create', [SysAppHotelController::class, 'create'])
                ->name('hotels.create');

            Route::post('/hotels', [SysAppHotelController::class, 'store'])
                ->name('hotels.store');

            Route::get('/hotels/{hotel}/edit', [SysAppHotelController::class, 'edit'])
                ->name('hotels.edit');

            Route::put('/hotels/{hotel}', [SysAppHotelController::class, 'update'])
                ->name('hotels.update');

            Route::get('/hotels/{hotel}/print-access', [SysAppHotelController::class, 'printAccess'])
                ->name('hotels.print-access');


                /*
|--------------------------------------------------------------------------
| Licencias / trials de hoteles
|--------------------------------------------------------------------------
*/

Route::post('/hotels/{hotel}/license/trial', [SysAppHotelController::class, 'activateTrial'])
    ->name('hotels.license.trial');

Route::post('/hotels/{hotel}/license/monthly-lite', [SysAppHotelController::class, 'activateMonthlyLite'])
    ->name('hotels.license.monthly-lite');

Route::post('/hotels/{hotel}/license/annual-lite', [SysAppHotelController::class, 'activateAnnualLite'])
    ->name('hotels.license.annual-lite');

Route::post('/hotels/{hotel}/license/suspend', [SysAppHotelController::class, 'suspendLicense'])
    ->name('hotels.license.suspend');

            /*
            |--------------------------------------------------------------------------
            | QRs administrados por SysApp
            |--------------------------------------------------------------------------
            */

            Route::get('/hotels/{hotel}/qr-points', [SysAppQrPointController::class, 'index'])
                ->name('hotels.qr-points.index');

            Route::post('/hotels/{hotel}/qr-points', [SysAppQrPointController::class, 'store'])
                ->name('hotels.qr-points.store');

            Route::post('/hotels/{hotel}/qr-points/generate-rooms', [SysAppQrPointController::class, 'generateRooms'])
                ->name('hotels.qr-points.generate-rooms');

            Route::get('/hotels/{hotel}/qr-points/print/all', [SysAppQrPointController::class, 'printAll'])
                ->name('hotels.qr-points.print-all');

            Route::get('/hotels/{hotel}/qr-points/{point}/print', [SysAppQrPointController::class, 'printOne'])
                ->name('hotels.qr-points.print');

            Route::patch('/hotels/{hotel}/qr-points/{point}/toggle', [SysAppQrPointController::class, 'toggle'])
                ->name('hotels.qr-points.toggle');

            Route::patch('/hotels/{hotel}/qr-points/{point}/invalidate', [SysAppQrPointController::class, 'invalidate'])
                ->name('hotels.qr-points.invalidate');

            Route::patch('/hotels/{hotel}/qr-points/{point}/regenerate', [SysAppQrPointController::class, 'regenerate'])
                ->name('hotels.qr-points.regenerate');

                Route::put('/hotels/{hotel}/qr-points/{point}', [SysAppQrPointController::class, 'update'])
    ->name('hotels.qr-points.update');

            /*
            |--------------------------------------------------------------------------
            | Solicitudes de QRs hechas por hoteles
            |--------------------------------------------------------------------------
            */

            Route::get('/qr-requests', [SysAppQrCreationRequestController::class, 'index'])
                ->name('qr-requests.index');

            Route::post('/qr-requests/{qrRequest}/approve', [SysAppQrCreationRequestController::class, 'approve'])
                ->middleware('throttle:30,1')
                ->name('qr-requests.approve');

            Route::post('/qr-requests/{qrRequest}/reject', [SysAppQrCreationRequestController::class, 'reject'])
                ->name('qr-requests.reject');

            /*
            |--------------------------------------------------------------------------
            | Solicitudes de reset de PIN
            |--------------------------------------------------------------------------
            */

            Route::get('/pin-reset-requests', [SysAppPinResetRequestController::class, 'index'])
                ->name('pin-reset-requests.index');

            Route::post('/pin-reset-requests/{pinRequest}/complete', [SysAppPinResetRequestController::class, 'complete'])
                ->name('pin-reset-requests.complete');

            Route::post('/pin-reset-requests/{pinRequest}/reject', [SysAppPinResetRequestController::class, 'reject'])
                ->name('pin-reset-requests.reject');
        });
    });