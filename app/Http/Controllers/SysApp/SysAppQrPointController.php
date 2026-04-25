<?php

namespace App\Http\Controllers\SysApp;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelQrPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SysAppQrPointController extends Controller
{
    public function index(Hotel $hotel)
    {
        $points = $hotel->qrPoints()
            ->latest()
            ->paginate(50);

        $requestTypes = config('hoteldesk.request_types');

        return view('sysapp.hotels.qr-points', compact('hotel', 'points', 'requestTypes'));
    }

    public function store(Request $request, Hotel $hotel)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in(['room', 'lobby', 'area', 'restaurant', 'parking', 'reception', 'other'])],
            'floor' => ['nullable', 'string', 'max:30'],
            'mode' => ['required', Rule::in(['menu', 'limited', 'direct'])],
            'fixed_request_type' => ['nullable', 'string', Rule::in(array_keys(config('hoteldesk.request_types')))],
            'allowed_request_types' => ['nullable', 'array'],
            'allowed_request_types.*' => ['string', Rule::in(array_keys(config('hoteldesk.request_types')))],
        ]);

        $mode = $data['mode'];

        HotelQrPoint::create([
            'hotel_id' => $hotel->id,
            'label' => $data['label'],
            'type' => $data['type'],
            'floor' => $data['floor'] ?? null,
            'public_code' => $this->makeUniquePublicCode($hotel),
            'mode' => $mode,
            'fixed_request_type' => $mode === 'direct' ? ($data['fixed_request_type'] ?? null) : null,
            'allowed_request_types' => $mode === 'limited' ? array_values($data['allowed_request_types'] ?? []) : null,
            'active' => true,
        ]);

        return back()->with('success', 'Punto QR creado correctamente.');
    }

    public function generateRooms(Request $request, Hotel $hotel)
    {
        $data = $request->validate([
            'from' => ['required', 'integer', 'min:1', 'max:9999'],
            'to' => ['required', 'integer', 'min:1', 'max:9999', 'gte:from'],
            'floor' => ['nullable', 'string', 'max:30'],
            'prefix' => ['nullable', 'string', 'max:40'],
        ]);

        $created = 0;

        for ($number = $data['from']; $number <= $data['to']; $number++) {
            $label = trim(($data['prefix'] ?: 'Habitación') . ' ' . $number);

            $exists = $hotel->qrPoints()
                ->where('label', $label)
                ->exists();

            if ($exists) {
                continue;
            }

            HotelQrPoint::create([
                'hotel_id' => $hotel->id,
                'label' => $label,
                'type' => 'room',
                'floor' => $data['floor'] ?? null,
                'public_code' => $this->makeUniquePublicCode($hotel),
                'mode' => 'menu',
                'fixed_request_type' => null,
                'allowed_request_types' => null,
                'active' => true,
            ]);

            $created++;
        }

        return back()->with('success', "Habitaciones generadas: {$created}.");
    }

    public function toggle(Hotel $hotel, HotelQrPoint $point)
    {
        $this->ensurePointBelongsToHotel($hotel, $point);

        $point->update([
            'active' => ! $point->active,
        ]);

        return back()->with('success', 'Estado del punto QR actualizado.');
    }

    public function printOne(Hotel $hotel, HotelQrPoint $point)
    {
        $this->ensurePointBelongsToHotel($hotel, $point);

        $url = route('public.qr.show', $point->public_code);

        $qrSvg = QrCode::format('svg')
            ->size(260)
            ->margin(2)
            ->generate($url);

        return view('sysapp.print.point', compact('hotel', 'point', 'url', 'qrSvg'));
    }

    public function printAll(Hotel $hotel)
    {
        $points = $hotel->qrPoints()
            ->where('active', true)
            ->orderBy('type')
            ->orderBy('floor')
            ->orderBy('label')
            ->get();

        $qrItems = $points->map(function (HotelQrPoint $point) {
            $url = route('public.qr.show', $point->public_code);

            return [
                'point' => $point,
                'url' => $url,
                'qrSvg' => QrCode::format('svg')
                    ->size(220)
                    ->margin(2)
                    ->generate($url),
            ];
        });

        return view('sysapp.print.all', compact('hotel', 'qrItems'));
    }

    private function makeUniquePublicCode(Hotel $hotel): string
    {
        do {
            $code = 'HD' . $hotel->id . Str::upper(Str::random(8));
        } while (HotelQrPoint::where('public_code', $code)->exists());

        return $code;
    }

    private function ensurePointBelongsToHotel(Hotel $hotel, HotelQrPoint $point): void
    {
        abort_if((int) $point->hotel_id !== (int) $hotel->id, 404);
    }
}