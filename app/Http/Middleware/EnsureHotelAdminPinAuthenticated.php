<?php

namespace App\Http\Middleware;

use App\Models\Hotel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHotelAdminPinAuthenticated
{
    private const ADMIN_SESSION_TTL_MINUTES = 30;

    public function handle(Request $request, Closure $next): Response
    {
        $hotel = $request->route('hotel');

        if (! $hotel instanceof Hotel) {
            abort(404);
        }

        if (! session()->get($hotel->sessionKey())) {
            return redirect()->route('hotel.login', $hotel);
        }

        $isAdmin = session()->get($hotel->adminSessionKey());
        $verifiedAt = (int) session()->get($hotel->adminVerifiedAtSessionKey(), 0);

        $isExpired = $verifiedAt <= 0
            || now()->diffInMinutes(\Carbon\Carbon::createFromTimestamp($verifiedAt)) >= self::ADMIN_SESSION_TTL_MINUTES;

        if (! $isAdmin || $isExpired) {
            session()->forget([
                $hotel->adminSessionKey(),
                $hotel->adminVerifiedAtSessionKey(),
            ]);

            session()->put('hotel_admin_intended_url', $request->fullUrl());

            return redirect()->route('hotel.admin-pin.show', $hotel);
        }

        return $next($request);
    }
}