<?php

namespace App\Http\Middleware;

use App\Models\Hotel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHotelPinAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $hotel = $request->route('hotel');

        if (! $hotel instanceof Hotel) {
            abort(404);
        }

        if (! session()->get($hotel->sessionKey())) {
            return redirect()->route('hotel.login', $hotel);
        }

        return $next($request);
    }
}