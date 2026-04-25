<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HotelPinSettingsController extends Controller
{
    public function edit(Hotel $hotel)
    {
        return view('hotel-panel.settings.pin', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $data = $request->validate([
            'current_pin' => ['required', 'string', 'min:4', 'max:12'],
            'pin' => ['required', 'string', 'min:4', 'max:12', 'confirmed'],
        ]);

        if (! Hash::check($data['current_pin'], $hotel->pin_hash)) {
            return back()
                ->withErrors(['current_pin' => 'El PIN actual no es correcto.'])
                ->withInput();
        }

        $hotel->update([
            'pin_hash' => Hash::make($data['pin']),
        ]);

        return back()->with('success', 'PIN actualizado correctamente.');
    }
}