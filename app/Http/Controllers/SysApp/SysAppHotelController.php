<?php

namespace App\Http\Controllers\SysApp;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SysAppHotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::query()
            ->withCount(['qrPoints', 'requests'])
            ->latest()
            ->paginate(15);

        return view('sysapp.hotels.index', compact('hotels'));
    }

    public function create()
    {
        $hotel = new Hotel([
            'status' => 'draft',
            'public_requests_enabled' => true,
            'panel_enabled' => true,
            'taxi_enabled' => true,
            'primary_color' => '#0F6CBD',
        ]);

        return view('sysapp.hotels.form', compact('hotel'));
    }

    public function store(Request $request)
    {
        $data = $this->validateHotel($request);

        $hotel = new Hotel();

        $hotel->fill([
    'name' => $data['name'],
    'slug' => $data['slug'] ?: Str::slug($data['name']),
    'phone' => $data['phone'] ?? null,
    'email' => $data['email'] ?? null,
    'address' => $data['address'] ?? null,
    'service_point_url' => $data['service_point_url'] ?? null,
    'status' => $data['status'],
    'public_requests_enabled' => $request->boolean('public_requests_enabled'),
    'panel_enabled' => $request->boolean('panel_enabled'),
    'taxi_enabled' => $request->boolean('taxi_enabled'),
    'primary_color' => $data['primary_color'] ?: '#0F6CBD',
]);

        $hotel->pin_hash = Hash::make($data['pin']);

        $hotel->save();

        if ($request->hasFile('logo')) {
            $hotel->logo_path = $request->file('logo')->store('hotels/' . $hotel->id, 'public');
            $hotel->save();
        }

        return redirect()
            ->route('sysapp.hotels.qr-points.index', $hotel)
            ->with('success', 'Hotel creado correctamente.');
    }

    public function edit(Hotel $hotel)
    {
        return view('sysapp.hotels.form', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $data = $this->validateHotel($request, $hotel);

      $hotel->fill([
    'name' => $data['name'],
    'slug' => $data['slug'] ?: Str::slug($data['name']),
    'phone' => $data['phone'] ?? null,
    'email' => $data['email'] ?? null,
    'address' => $data['address'] ?? null,
    'service_point_url' => $data['service_point_url'] ?? null,
    'status' => $data['status'],
    'public_requests_enabled' => $request->boolean('public_requests_enabled'),
    'panel_enabled' => $request->boolean('panel_enabled'),
    'taxi_enabled' => $request->boolean('taxi_enabled'),
    'primary_color' => $data['primary_color'] ?: '#0F6CBD',
]);

        if (! empty($data['pin'])) {
            $hotel->pin_hash = Hash::make($data['pin']);
        }

        if ($request->hasFile('logo')) {
            if ($hotel->logo_path) {
                Storage::disk('public')->delete($hotel->logo_path);
            }

            $hotel->logo_path = $request->file('logo')->store('hotels/' . $hotel->id, 'public');
        }

        $hotel->save();

        return redirect()
            ->route('sysapp.hotels.edit', $hotel)
            ->with('success', 'Hotel actualizado correctamente.');
    }

    private function validateHotel(Request $request, ?Hotel $hotel = null): array
    {
        $hotelId = $hotel?->id;

        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'nullable',
                'string',
                'max:120',
                'alpha_dash',
                Rule::unique('hotels', 'slug')->ignore($hotelId),
            ],
            'pin' => [$hotel ? 'nullable' : 'required', 'string', 'min:4', 'max:12'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'active', 'paused', 'disabled'])],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'service_point_url' => ['nullable', 'url', 'max:255'],
        ]);
    }

    public function printAccess(Hotel $hotel)
{
    $panelUrl = route('hotel.login', $hotel);
    $accessUrl = route('public.access.create');
    $accessCode = $hotel->slug;

    $qrSvg = QrCode::format('svg')
        ->size(240)
        ->margin(1)
        ->generate($panelUrl);

    return view('sysapp.hotels.print-access', compact(
        'hotel',
        'panelUrl',
        'accessUrl',
        'accessCode',
        'qrSvg'
    ));
}
}