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

use App\Services\HotelDesk\HotelLicenseService;
use Illuminate\Http\RedirectResponse;

class SysAppHotelController extends Controller
{
    public function index(Request $request)
{
    $search = trim((string) $request->query('q', ''));
    $status = (string) $request->query('status', '');

    $baseQuery = Hotel::query();

    $summary = [
        'total' => (clone $baseQuery)->count(),
        'active' => (clone $baseQuery)->where('status', 'active')->count(),
        'paused' => (clone $baseQuery)->where('status', 'paused')->count(),
        'disabled' => (clone $baseQuery)->where('status', 'disabled')->count(),
        'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
        'qr_total' => \App\Models\HotelQrPoint::query()->count(),
        'requests_total' => \App\Models\HotelRequest::query()->count(),
    ];

    $hotels = Hotel::query()
        ->withCount(['qrPoints', 'requests'])
        ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        })
        ->when($status !== '', function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->latest()
        ->paginate(15)
        ->withQueryString();

    return view('sysapp.hotels.index', compact(
        'hotels',
        'summary',
        'search',
        'status'
    ));
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
$hotel->admin_pin_hash = Hash::make($data['admin_pin'] ?: $data['pin']);
$hotel->admin_pin_changed_at = now();

$hotel->save();

        if ($request->hasFile('logo')) {
            $hotel->logo_path = $request->file('logo')->store('hotels/' . $hotel->id, 'public');
            $hotel->save();
        }

      return redirect()
    ->route('sysapp.hotels.edit', $hotel)
    ->with('success', 'Hotel creado correctamente. Ahora puedes preparar la prueba de 14 días.');
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
        if (! empty($data['admin_pin'])) {
    $hotel->admin_pin_hash = Hash::make($data['admin_pin']);
    $hotel->admin_pin_changed_at = now();
    $hotel->admin_failed_attempts = 0;
    $hotel->admin_locked_until = null;
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
            'admin_pin' => ['nullable', 'string', 'min:4', 'max:12'],
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


public function activateTrial(Hotel $hotel, HotelLicenseService $licenses): RedirectResponse
{
    /*
    |--------------------------------------------------------------------------
    | Acceso inicial para demo
    |--------------------------------------------------------------------------
    |
    | Al preparar una prueba desde SysApp, dejamos credenciales iniciales
    | controladas para que el hotel pueda entrar sin depender de Tinker.
    |
    | PIN recepción: 1234
    | PIN admin:     4321
    |
    | El trial NO inicia aquí. Solo queda preparado. El conteo empieza cuando
    | el hotel acepta los términos desde el panel.
    |
    */

    $hotel->forceFill([
        'pin_hash' => Hash::make('1234'),
        'admin_pin_hash' => Hash::make('4321'),
        'admin_pin_changed_at' => null,
        'admin_failed_attempts' => 0,
        'admin_locked_until' => null,
    ])->save();

    $licenses->prepareTrial($hotel->fresh(), 14);

    return back()->with(
        'success',
        'Prueba preparada correctamente. PIN recepción: 1234. PIN admin: 4321. El conteo iniciará cuando el hotel acepte los términos al entrar por primera vez.'
    );
}

public function activateMonthlyLite(Hotel $hotel, HotelLicenseService $licenses): RedirectResponse
{
    $licenses->activateMonthlyLite($hotel);

    return back()->with('success', 'Licencia mensual Lite activada correctamente.');
}

public function activateAnnualLite(Hotel $hotel, HotelLicenseService $licenses): RedirectResponse
{
    $licenses->activateAnnualLite($hotel);

    return back()->with('success', 'Licencia anual Lite activada correctamente.');
}

public function suspendLicense(Hotel $hotel, HotelLicenseService $licenses): RedirectResponse
{
    $licenses->suspend($hotel, 'Suspendido desde SysApp.');

    return back()->with('success', 'Licencia suspendida correctamente.');
}



}