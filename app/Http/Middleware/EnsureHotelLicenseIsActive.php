<?php

namespace App\Http\Middleware;

use App\Models\Hotel;
use App\Models\HotelQrPoint;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHotelLicenseIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $hotel = $this->resolveHotel($request);

        if (! $hotel instanceof Hotel) {
            return $next($request);
        }

        if ($hotel->isLicenseActive()) {
            return $next($request);
        }

        if ($hotel->isTrialPending() && $request->route('hotel')) {
            return redirect()->route('hotel.terms.show', $hotel);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'message' => 'La licencia de HotelDesk Lite no está activa.',
                'license_status' => $hotel->license_status,
            ], 402);
        }

        return response()->view('hotel-panel.license-blocked', [
            'hotel' => $hotel,
        ], 402);
    }

    private function resolveHotel(Request $request): ?Hotel
    {
        $hotel = $request->route('hotel');

        if ($hotel instanceof Hotel) {
            return $hotel;
        }

        if (is_string($hotel)) {
            return Hotel::where('slug', $hotel)->first();
        }

        $code = $request->route('code');

        if (is_string($code) && $code !== '') {
            $point = HotelQrPoint::with('hotel')
                ->where('public_code', $code)
                ->first();

            return $point?->hotel;
        }

        return null;
    }
}