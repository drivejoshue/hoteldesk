<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('hoteldesk-public-qr-request', function (Request $request) {
            $code = (string) ($request->route('code') ?? 'unknown');
            $ip = (string) ($request->ip() ?: 'unknown');

            $message = 'Hemos recibido varias solicitudes desde este código QR en poco tiempo. Si necesitas apoyo urgente, por favor comunícate directamente con recepción.';

            $response = function (Request $request, array $headers) use ($message) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'ok' => false,
                        'code' => 'too_many_hotel_requests',
                        'message' => $message,
                    ], 429, $headers);
                }

                return back()
                    ->withInput()
                    ->with('warning', $message)
                    ->withHeaders($headers);
            };

            return [
                Limit::perMinute(2)
                    ->by("hoteldesk:qr:minute:{$code}:{$ip}")
                    ->response($response),

                Limit::perHour(5)
                    ->by("hoteldesk:qr:hour:{$code}")
                    ->response($response),

                Limit::perDay(10)
                    ->by("hoteldesk:qr:day:{$code}")
                    ->response($response),
            ];
        });
    }
}