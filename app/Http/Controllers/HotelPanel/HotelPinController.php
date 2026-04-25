<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HotelPinController extends Controller
{
    public function showLogin(Hotel $hotel)
    {
        if (! $hotel->isPanelAvailable()) {
            return view('hotel-panel.unavailable', compact('hotel'));
        }

        if (session()->get($hotel->sessionKey())) {
            return redirect()->route('hotel.dashboard', $hotel);
        }

        return view('hotel-panel.login', compact('hotel'));
    }

    public function login(Request $request, Hotel $hotel)
    {
        if (! $hotel->isPanelAvailable()) {
            return view('hotel-panel.unavailable', compact('hotel'));
        }

        $data = $request->validate([
            'pin' => ['required', 'string', 'min:4', 'max:12'],
        ]);

        if (! Hash::check($data['pin'], $hotel->pin_hash)) {
            return back()
                ->withErrors(['pin' => 'PIN incorrecto.'])
                ->withInput();
        }

        session()->put($hotel->sessionKey(), true);

        return redirect()->route('hotel.dashboard', $hotel);
    }

    public function logout(Hotel $hotel)
    {
        session()->forget($hotel->sessionKey());

        return redirect()->route('hotel.login', $hotel);
    }
}