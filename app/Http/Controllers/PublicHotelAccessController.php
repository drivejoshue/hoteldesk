<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicHotelAccessController extends Controller
{
    public function create()
    {
        return view('public-hotel.access', [
            'hotel' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:120'],
        ], [
            'code.required' => 'Escribe el código del hotel.',
        ]);

        $code = Str::slug(trim($data['code']));

        $hotel = Hotel::query()
            ->where('slug', $code)
            ->first();

        if (! $hotel) {
            return back()
                ->withInput()
                ->withErrors([
                    'code' => 'No encontramos un hotel con ese código.',
                ]);
        }

        if ($hotel->status === 'disabled' || ! $hotel->panel_enabled) {
            return back()
                ->withInput()
                ->withErrors([
                    'code' => 'El panel de este hotel no está disponible por el momento.',
                ]);
        }

        return redirect()->route('hotel.login', $hotel);
    }
}