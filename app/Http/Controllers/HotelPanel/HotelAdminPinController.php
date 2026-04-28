<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HotelAdminPinController extends Controller
{
    public function show(Hotel $hotel)
    {
        if (! $hotel->isPanelAvailable()) {
            return view('hotel-panel.unavailable', compact('hotel'));
        }

        return view('hotel-panel.admin-pin.login', compact('hotel'));
    }

    public function verify(Request $request, Hotel $hotel)
    {
        if (! $hotel->isPanelAvailable()) {
            return view('hotel-panel.unavailable', compact('hotel'));
        }

        if ($hotel->isAdminPinLocked()) {
            return back()
                ->withErrors([
                    'pin' => 'El acceso administrativo está bloqueado temporalmente. Intenta más tarde.',
                ]);
        }

        $data = $request->validate([
            'pin' => ['required', 'string', 'min:4', 'max:12'],
            'intended' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $hotel->admin_pin_hash || ! Hash::check($data['pin'], $hotel->admin_pin_hash)) {
            $hotel->registerAdminPinFailedAttempt();

            return back()
                ->withErrors(['pin' => 'PIN administrativo incorrecto.'])
                ->withInput();
        }

        $hotel->resetAdminPinAttempts();

        session()->put($hotel->adminSessionKey(), true);
        session()->put($hotel->adminVerifiedAtSessionKey(), now()->timestamp);

        $intended = $data['intended'] ?? session()->pull('hotel_admin_intended_url');

        if ($intended && str_starts_with($intended, url('/h/' . $hotel->slug))) {
            return redirect()->to($intended);
        }

        return redirect()->route('hotel.reports.index', $hotel);
    }

    public function logout(Hotel $hotel)
    {
        session()->forget([
            $hotel->adminSessionKey(),
            $hotel->adminVerifiedAtSessionKey(),
        ]);

        return redirect()->route('hotel.dashboard', $hotel)
            ->with('success', 'Saliste del modo administrador.');
    }
}