<?php

namespace App\Http\Controllers\SysApp;

use App\Http\Controllers\Controller;
use App\Models\HotelQrCreationRequest;
use App\Models\HotelQrPoint;
use App\Models\SysAppAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SysAppQrCreationRequestController extends Controller
{
    public function index()
    {
        $requests = HotelQrCreationRequest::query()
            ->with(['hotel', 'createdQrPoint'])
            ->latest()
            ->paginate(20);

        return view('sysapp.qr-requests.index', compact('requests'));
    }

    public function approve(Request $request, HotelQrCreationRequest $qrRequest)
    {
        if ($qrRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'Esta solicitud ya fue revisada.']);
        }

        DB::transaction(function () use ($request, $qrRequest) {
            $hotel = $qrRequest->hotel;

            $point = HotelQrPoint::create([
                'hotel_id' => $hotel->id,
                'label' => $qrRequest->label,
                'type' => $qrRequest->type,
                'floor' => $qrRequest->floor,
                'public_code' => $this->makeUniquePublicCode((int) $hotel->id),
                'mode' => $qrRequest->mode,
                'fixed_request_type' => $qrRequest->mode === 'direct'
                    ? $qrRequest->fixed_request_type
                    : null,
                'allowed_request_types' => $qrRequest->mode === 'limited'
                    ? $qrRequest->allowed_request_types
                    : null,
                'active' => true,
            ]);

            $qrRequest->update([
                'status' => 'approved',
                'reviewed_by' => session('hoteldesk.sysapp.admin_id'),
                'reviewed_at' => now(),
                'created_qr_point_id' => $point->id,
                'reject_reason' => null,
            ]);

            $this->audit(
                action: 'hotel_qr_request_approved',
                description: 'Solicitud de QR aprobada.',
                request: $request,
                hotelId: $hotel->id,
                meta: [
                    'qr_request_id' => $qrRequest->id,
                    'created_qr_point_id' => $point->id,
                    'label' => $point->label,
                ]
            );
        });

        return back()->with('success', 'QR aprobado y creado correctamente.');
    }

    public function reject(Request $request, HotelQrCreationRequest $qrRequest)
    {
        if ($qrRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'Esta solicitud ya fue revisada.']);
        }

        $data = $request->validate([
            'reject_reason' => ['required', 'string', 'max:500'],
        ]);

        $qrRequest->update([
            'status' => 'rejected',
            'reviewed_by' => session('hoteldesk.sysapp.admin_id'),
            'reviewed_at' => now(),
            'reject_reason' => $data['reject_reason'],
        ]);

        $this->audit(
            action: 'hotel_qr_request_rejected',
            description: 'Solicitud de QR rechazada.',
            request: $request,
            hotelId: $qrRequest->hotel_id,
            meta: [
                'qr_request_id' => $qrRequest->id,
                'reject_reason' => $data['reject_reason'],
            ]
        );

        return back()->with('success', 'Solicitud rechazada correctamente.');
    }

    private function makeUniquePublicCode(int $hotelId): string
    {
        do {
            $code = 'HD' . $hotelId . Str::upper(Str::random(8));
        } while (HotelQrPoint::query()->where('public_code', $code)->exists());

        return $code;
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