<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelQrPoint;
use App\Models\SysAppAuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HotelQrPointController extends Controller
{
    public function index(Hotel $hotel)
    {
        $points = $hotel->qrPoints()
            ->orderBy('type')
            ->orderBy('floor')
            ->orderBy('label')
            ->paginate(40);

        return view('hotel-panel.qr-points.index', compact('hotel', 'points'));
    }

    public function print(Request $request, Hotel $hotel, HotelQrPoint $point)
    {
        $this->ensurePointBelongsToHotel($hotel, $point);

        abort_if(! $point->active, 404);

        $size = $this->resolvePrintSize($request->query('size', 'half'));
        $qrSize = $this->qrPixelSize($size);

        $url = route('public.qr.show', $point->public_code);

        $qrSvg = QrCode::format('svg')
            ->size($qrSize)
            ->margin(2)
            ->generate($url);

        return view('hotel-panel.qr-points.print', compact(
            'hotel',
            'point',
            'url',
            'qrSvg',
            'size'
        ));
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
            ?? 'Invalidado desde el panel del hotel.';

        $point->forceFill([
            'active' => false,
            'invalidated_at' => now(),
            'invalidated_reason' => $reason,
        ])->save();

        $this->audit(
            request: $request,
            hotel: $hotel,
            action: 'hotel_qr_invalidated',
            description: 'QR invalidado por administrador del hotel.',
            meta: [
                'actor' => 'hotel_admin',
                'qr_point_id' => $point->id,
                'label' => $point->label,
                'public_code' => $point->public_code,
                'reason' => $reason,
            ]
        );

        return back()->with('success', 'QR invalidado correctamente. Ese código ya no aceptará solicitudes.');
    }

    public function regenerate(Request $request, Hotel $hotel, HotelQrPoint $point): RedirectResponse
    {
        $this->ensurePointBelongsToHotel($hotel, $point);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $reason = $this->normalizeReason($data['reason'] ?? null)
            ?? 'Regenerado desde el panel del hotel.';

        $oldCode = $point->public_code;
        $newCode = $this->generateUniquePublicCode($hotel);

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
            action: 'hotel_qr_regenerated',
            description: 'QR regenerado por administrador del hotel.',
            meta: [
                'actor' => 'hotel_admin',
                'qr_point_id' => $point->id,
                'label' => $point->label,
                'old_public_code' => $oldCode,
                'new_public_code' => $newCode,
                'reason' => $reason,
            ]
        );

        return back()->with('success', 'QR regenerado correctamente. El código anterior dejó de funcionar.');
    }

    private function resolvePrintSize(?string $size): string
    {
        return in_array($size, ['quarter', 'half', 'letter'], true)
            ? $size
            : 'half';
    }

    private function qrPixelSize(string $size): int
    {
        return match ($size) {
            'quarter' => 210,
            'letter' => 340,
            default => 270,
        };
    }

    private function ensurePointBelongsToHotel(Hotel $hotel, HotelQrPoint $point): void
    {
        abort_if((int) $point->hotel_id !== (int) $hotel->id, 404);
    }

    private function generateUniquePublicCode(Hotel $hotel): string
    {
        do {
            $code = 'HD' . $hotel->id . Str::upper(Str::random(14));
        } while (HotelQrPoint::query()->where('public_code', $code)->exists());

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

    private function audit(
        Request $request,
        Hotel $hotel,
        string $action,
        string $description,
        array $meta = []
    ): void {
        SysAppAuditLog::create([
            'admin_id' => null,
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