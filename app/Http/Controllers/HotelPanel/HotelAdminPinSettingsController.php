<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HotelAdminPinSettingsController extends Controller
{
    public function edit(Hotel $hotel)
    {
        return view('hotel-panel.admin-pin.settings', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        if ($hotel->isAdminPinLocked()) {
            return back()
                ->withErrors([
                    'current_admin_pin' => 'El acceso administrativo está bloqueado temporalmente. Intenta más tarde.',
                ]);
        }

        $data = $request->validate([
            'current_admin_pin' => ['required', 'string', 'min:4', 'max:12'],
            'admin_pin' => ['required', 'string', 'min:4', 'max:12', 'confirmed'],
        ], [
            'current_admin_pin.required' => 'Escribe el PIN admin actual.',
            'admin_pin.required' => 'Escribe el nuevo PIN admin.',
            'admin_pin.confirmed' => 'La confirmación del nuevo PIN admin no coincide.',
        ]);

        if (! $hotel->admin_pin_hash || ! Hash::check($data['current_admin_pin'], $hotel->admin_pin_hash)) {
            $hotel->registerAdminPinFailedAttempt();

            return back()
                ->withErrors([
                    'current_admin_pin' => 'El PIN admin actual no es correcto.',
                ])
                ->withInput();
        }

        $hotel->forceFill([
            'admin_pin_hash' => Hash::make($data['admin_pin']),
            'admin_pin_changed_at' => now(),
            'admin_failed_attempts' => 0,
            'admin_locked_until' => null,
        ])->save();

        return back()->with('success', 'PIN admin actualizado correctamente.');
    }
}