<?php

use App\Http\Controllers\HotelPanel\HotelDashboardController;
use App\Http\Controllers\HotelPanel\HotelPinController;
use App\Http\Controllers\HotelPanel\HotelPinResetRequestController;
use App\Http\Controllers\HotelPanel\HotelPinSettingsController;
use App\Http\Controllers\HotelPanel\HotelQrCreationRequestController;
use App\Http\Controllers\HotelPanel\HotelQrPointController;
use App\Http\Controllers\HotelPanel\HotelRequestStatusController;
use App\Http\Controllers\PublicQrRequestController;
use App\Http\Controllers\SysApp\SysAppAuthController;
use App\Http\Controllers\SysApp\SysAppHotelController;
use App\Http\Controllers\SysApp\SysAppPinResetRequestController;
use App\Http\Controllers\SysApp\SysAppQrCreationRequestController;
use App\Http\Controllers\SysApp\SysAppQrPointController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SysApp\SysAppAuditLogController;

Route::get('/', function () {
    return redirect()->route('sysapp.login');
});

/*
|--------------------------------------------------------------------------
| QR público del huésped
|--------------------------------------------------------------------------
*/

Route::get('/r/{code}', [PublicQrRequestController::class, 'show'])
    ->name('public.qr.show');

Route::post('/r/{code}', [PublicQrRequestController::class, 'store'])
    ->middleware('throttle:15,1')
    ->name('public.qr.store');
/*
|--------------------------------------------------------------------------
| Panel del hotel
|--------------------------------------------------------------------------
*/

Route::prefix('h/{hotel:slug}')
    ->name('hotel.')
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Acceso por PIN
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
        | Panel protegido por PIN del hotel
        |--------------------------------------------------------------------------
        */

        Route::middleware('hotel.pin')->group(function () {
            Route::post('/logout', [HotelPinController::class, 'logout'])
                ->name('logout');

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

            /*
            |--------------------------------------------------------------------------
            | QRs visibles para el hotel
            |--------------------------------------------------------------------------
            */

            Route::get('/qr-points', [HotelQrPointController::class, 'index'])
                ->name('qr-points.index');

            Route::get('/qr-points/{point}/print', [HotelQrPointController::class, 'print'])
                ->name('qr-points.print');

            /*
            |--------------------------------------------------------------------------
            | Solicitudes de nuevos QRs
            |--------------------------------------------------------------------------
            */

            Route::get('/qr-requests', [HotelQrCreationRequestController::class, 'index'])
                ->name('qr-requests.index');

            Route::get('/qr-requests/create', [HotelQrCreationRequestController::class, 'create'])
                ->name('qr-requests.create');

           Route::post('/qr-requests', [HotelQrCreationRequestController::class, 'store'])
    ->middleware('throttle:10,10')
    ->name('qr-requests.store');

            /*
            |--------------------------------------------------------------------------
            | Seguridad del hotel
            |--------------------------------------------------------------------------
            */

            Route::get('/settings/pin', [HotelPinSettingsController::class, 'edit'])
                ->name('settings.pin.edit');

            Route::put('/settings/pin', [HotelPinSettingsController::class, 'update'])
                ->name('settings.pin.update');
        });
    });

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

            Route::patch('/hotels/{hotel}/qr-points/{point}/toggle', [SysAppQrPointController::class, 'toggle'])
                ->name('hotels.qr-points.toggle');

            Route::get('/hotels/{hotel}/qr-points/{point}/print', [SysAppQrPointController::class, 'printOne'])
                ->name('hotels.qr-points.print');

            Route::get('/hotels/{hotel}/qr-points/print/all', [SysAppQrPointController::class, 'printAll'])
                ->name('hotels.qr-points.print-all');

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