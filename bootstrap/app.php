<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureHotelPinAuthenticated;
use App\Http\Middleware\EnsureHotelAdminPinAuthenticated;
use App\Http\Middleware\EnsureSysAppAdminAuthenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'hotel.pin' => EnsureHotelPinAuthenticated::class,
            'hotel.admin.pin' => EnsureHotelAdminPinAuthenticated::class,
            'sysapp.auth' => EnsureSysAppAdminAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();