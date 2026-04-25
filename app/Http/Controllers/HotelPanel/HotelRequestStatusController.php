<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class HotelRequestStatusController extends Controller
{
    public function take(Hotel $hotel, int $request): JsonResponse
    {
        $hotelRequest = $this->findTenantRequest($hotel, $request);

        if ($hotelRequest->status !== 'pending') {
            return response()->json([
                'ok' => false,
                'message' => 'La solicitud ya no está pendiente.',
            ], 422);
        }

        $now = Carbon::now();

        $this->duplicateGroupQuery($hotel, $hotelRequest)
            ->where('status', 'pending')
            ->update([
                'status' => 'in_progress',
                'taken_at' => $now,
                'updated_at' => $now,
            ]);

        return response()->json([
            'ok' => true,
            'message' => 'Solicitud marcada en proceso.',
        ]);
    }

    public function complete(Hotel $hotel, int $request): JsonResponse
    {
        $hotelRequest = $this->findTenantRequest($hotel, $request);

        if (! in_array($hotelRequest->status, ['pending', 'in_progress'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'La solicitud no puede resolverse.',
            ], 422);
        }

        $now = Carbon::now();

        $this->duplicateGroupQuery($hotel, $hotelRequest)
            ->whereIn('status', ['pending', 'in_progress'])
            ->update([
                'status' => 'completed',
                'completed_at' => $now,
                'updated_at' => $now,
            ]);

        return response()->json([
            'ok' => true,
            'message' => 'Solicitud resuelta.',
        ]);
    }

    public function cancel(Hotel $hotel, int $request): JsonResponse
    {
        $hotelRequest = $this->findTenantRequest($hotel, $request);

        if (! in_array($hotelRequest->status, ['pending', 'in_progress'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'La solicitud no puede cancelarse.',
            ], 422);
        }

        $now = Carbon::now();

        $this->duplicateGroupQuery($hotel, $hotelRequest)
            ->whereIn('status', ['pending', 'in_progress'])
            ->update([
                'status' => 'canceled',
                'canceled_at' => $now,
                'updated_at' => $now,
            ]);

        return response()->json([
            'ok' => true,
            'message' => 'Solicitud cancelada.',
        ]);
    }

    private function findTenantRequest(Hotel $hotel, int $requestId): HotelRequest
    {
        return HotelRequest::query()
            ->where('hotel_id', $hotel->id)
            ->whereKey($requestId)
            ->firstOrFail();
    }

    /**
     * Grupo lógico de duplicados.
     *
     * Mismo hotel, mismo punto QR, mismo tipo y misma nota.
     * Esto evita que 5 taps repetidos aparezcan como 5 trabajos diferentes.
     */
    private function duplicateGroupQuery(Hotel $hotel, HotelRequest $request): Builder
    {
        return HotelRequest::query()
            ->where('hotel_id', $hotel->id)
            ->where('hotel_qr_point_id', $request->hotel_qr_point_id)
            ->where('type_key', $request->type_key)
            ->where(function (Builder $query) use ($request) {
                $note = trim((string) $request->note);

                if ($note === '') {
                    $query->whereNull('note')
                        ->orWhere('note', '');
                } else {
                    $query->where('note', $request->note);
                }
            });
    }
}