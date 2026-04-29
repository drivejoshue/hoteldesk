<?php

namespace App\Http\Controllers\SysApp;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelQrPoint;
use App\Models\SysAppAuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SysAppQrPointController extends Controller
{
    public function index(Hotel $hotel)
    {
        $points = $hotel->qrPoints()
            ->orderBy('type')
            ->orderBy('floor')
            ->orderBy('label')
            ->paginate(50);

        $requestTypes = config('hoteldesk.request_types');

        return view('sysapp.hotels.qr-points', compact('hotel', 'points', 'requestTypes'));
    }

    public function store(Request $request, Hotel $hotel): RedirectResponse
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

        $point = HotelQrPoint::create([
            'hotel_id' => $hotel->id,
            'label' => trim($data['label']),
            'type' => $data['type'],
            'floor' => $data['floor'] ?? null,
            'public_code' => $this->makeUniquePublicCode($hotel),
            'mode' => $mode,
            'fixed_request_type' => $mode === 'direct' ? ($data['fixed_request_type'] ?? null) : null,
            'allowed_request_types' => $mode === 'limited' ? array_values($data['allowed_request_types'] ?? []) : null,
            'active' => true,
        ]);

        $this->audit(
            request: $request,
            hotel: $hotel,
            action: 'sysapp_qr_created',
            description: 'QR creado desde SysApp.',
            meta: [
                'qr_point_id' => $point->id,
                'label' => $point->label,
                'public_code' => $point->public_code,
            ]
        );

        return back()->with('success', 'Punto QR creado correctamente.');
    }

    public function generateRooms(Request $request, Hotel $hotel): RedirectResponse
    {
        $data = $request->validate([
            'from' => ['required', 'integer', 'min:1', 'max:9999'],
            'to' => ['required', 'integer', 'min:1', 'max:9999', 'gte:from'],
            'floor' => ['nullable', 'string', 'max:30'],
            'prefix' => ['nullable', 'string', 'max:40'],
        ]);

        $created = 0;
        $createdIds = [];

        for ($number = $data['from']; $number <= $data['to']; $number++) {
            $label = trim(($data['prefix'] ?: 'Habitación') . ' ' . $number);

            $exists = $hotel->qrPoints()
                ->where('label', $label)
                ->exists();

            if ($exists) {
                continue;
            }

            $point = HotelQrPoint::create([
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
            $createdIds[] = $point->id;
        }

        $this->audit(
            request: $request,
            hotel: $hotel,
            action: 'sysapp_qr_rooms_generated',
            description: 'Rango de habitaciones QR generado desde SysApp.',
            meta: [
                'from' => $data['from'],
                'to' => $data['to'],
                'floor' => $data['floor'] ?? null,
                'prefix' => $data['prefix'] ?? null,
                'created' => $created,
                'created_ids' => $createdIds,
            ]
        );

        return back()->with('success', "Habitaciones generadas: {$created}.");
    }

    public function toggle(Request $request, Hotel $hotel, HotelQrPoint $point): RedirectResponse
    {
        $this->ensurePointBelongsToHotel($hotel, $point);

        $point->update([
            'active' => ! $point->active,
        ]);

        $this->audit(
            request: $request,
            hotel: $hotel,
            action: 'sysapp_qr_toggled',
            description: 'Estado del QR actualizado desde SysApp.',
            meta: [
                'qr_point_id' => $point->id,
                'label' => $point->label,
                'public_code' => $point->public_code,
                'active' => $point->active,
            ]
        );

        return back()->with('success', 'Estado del punto QR actualizado.');
    }

    public function invalidate(Request $request, Hotel $hotel, HotelQrPoint $point): RedirectResponse
    {
        $this->ensurePointBelongsToHotel($hotel, $point);

        if (! $point->active) {
            return back()->with('info', 'Este QR ya estaba inactivo.');
        }

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $reason = $this->normalizeReason($data['reason'] ?? null)
            ?? 'Invalidado desde SysApp.';

        $point->forceFill([
            'active' => false,
            'invalidated_at' => now(),
            'invalidated_reason' => $reason,
        ])->save();

        $this->audit(
            request: $request,
            hotel: $hotel,
            action: 'sysapp_qr_invalidated',
            description: 'QR invalidado desde SysApp.',
            meta: [
                'qr_point_id' => $point->id,
                'label' => $point->label,
                'public_code' => $point->public_code,
                'reason' => $reason,
            ]
        );

        return back()->with('success', 'QR invalidado correctamente.');
    }

    public function regenerate(Request $request, Hotel $hotel, HotelQrPoint $point): RedirectResponse
    {
        $this->ensurePointBelongsToHotel($hotel, $point);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $reason = $this->normalizeReason($data['reason'] ?? null)
            ?? 'Regenerado desde SysApp.';

        $oldCode = $point->public_code;
        $newCode = $this->makeUniquePublicCode($hotel);

        $point->forceFill([
            'public_code' => $newCode,
            'previous_public_code' => $oldCode,
            'active' => true,
            'regenerated_at' => now(),
            'invalidated_at' => null,
            'invalidated_reason' => null,
        ])->save();

        $this->audit(
            request: $request,
            hotel: $hotel,
            action: 'sysapp_qr_regenerated',
            description: 'QR regenerado desde SysApp.',
            meta: [
                'qr_point_id' => $point->id,
                'label' => $point->label,
                'old_public_code' => $oldCode,
                'new_public_code' => $newCode,
                'reason' => $reason,
            ]
        );

        return back()->with('success', 'QR regenerado correctamente. El código anterior dejó de funcionar.');
    }

    public function printOne(Hotel $hotel, HotelQrPoint $point)
    {
        $this->ensurePointBelongsToHotel($hotel, $point);

        abort_if(! $point->active, 404);

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
            $code = 'HD' . $hotel->id . Str::upper(Str::random(14));
        } while (HotelQrPoint::where('public_code', $code)->exists());

        return $code;
    }

    private function normalizeReason(?string $reason): ?string
    {
        if ($reason === null) {
            return null;
        }

        $reason = trim(strip_tags($reason));

        return $reason === '' ? null : Str::limit($reason, 255, '');
    }

    private function ensurePointBelongsToHotel(Hotel $hotel, HotelQrPoint $point): void
    {
        abort_if((int) $point->hotel_id !== (int) $hotel->id, 404);
    }

    private function audit(
        Request $request,
        Hotel $hotel,
        string $action,
        string $description,
        array $meta = []
    ): void {
        SysAppAuditLog::create([
            'admin_id' => session('hoteldesk.sysapp.admin_id'),
            'hotel_id' => $hotel->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
            'meta' => $meta ?: null,
            'created_at' => now(),
        ]);
    }
}