<?php

namespace App\Http\Controllers\SysApp;

use App\Http\Controllers\Controller;
use App\Models\HotelPinResetRequest;
use App\Models\SysAppAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SysAppPinResetRequestController extends Controller
{
    public function index()
    {
        $requests = HotelPinResetRequest::query()
            ->with('hotel')
            ->latest()
            ->paginate(20);

        return view('sysapp.pin-reset-requests.index', compact('requests'));
    }

    public function complete(Request $request, HotelPinResetRequest $pinRequest)
    {
        if ($pinRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'Esta solicitud ya fue revisada.']);
        }

        $hotel = $pinRequest->hotel;

        if (! $hotel) {
            return back()->withErrors(['hotel' => 'El hotel ya no existe.']);
        }

        $temporaryPin = (string) random_int(100000, 999999);

        $hotel->update([
            'pin_hash' => Hash::make($temporaryPin),
        ]);

        $pinRequest->update([
            'status' => 'completed',
            'reviewed_by' => session('hoteldesk.sysapp.admin_id'),
            'reviewed_at' => now(),
            'reject_reason' => null,
        ]);

        $this->audit(
            action: 'hotel_pin_reset_completed',
            description: 'PIN de hotel restablecido por SysApp.',
            request: $request,
            hotelId: $hotel->id,
            meta: [
                'pin_reset_request_id' => $pinRequest->id,
                'hotel_slug' => $hotel->slug,
            ]
        );

        return back()
            ->with('success', 'PIN restablecido correctamente.')
            ->with('temporary_pin', $temporaryPin)
            ->with('temporary_pin_hotel', $hotel->name);
    }

    public function reject(Request $request, HotelPinResetRequest $pinRequest)
    {
        if ($pinRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'Esta solicitud ya fue revisada.']);
        }

        $data = $request->validate([
            'reject_reason' => ['required', 'string', 'max:500'],
        ]);

        $pinRequest->update([
            'status' => 'rejected',
            'reviewed_by' => session('hoteldesk.sysapp.admin_id'),
            'reviewed_at' => now(),
            'reject_reason' => $data['reject_reason'],
        ]);

        $this->audit(
            action: 'hotel_pin_reset_rejected',
            description: 'Solicitud de reset de PIN rechazada.',
            request: $request,
            hotelId: $pinRequest->hotel_id,
            meta: [
                'pin_reset_request_id' => $pinRequest->id,
                'reject_reason' => $data['reject_reason'],
            ]
        );

        return back()->with('success', 'Solicitud rechazada correctamente.');
    }

    private function audit(
        string $action,
        string $description,
        Request $request,
        ?int $hotelId = null,
        array $meta = []
    ): void {
        SysAppAuditLog::create([
            'admin_id' => session('hoteldesk.sysapp.admin_id'),
            'hotel_id' => $hotelId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
            'meta' => $meta ?: null,
            'created_at' => now(),
        ]);
    }
}