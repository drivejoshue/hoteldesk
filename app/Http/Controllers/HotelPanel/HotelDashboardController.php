<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class HotelDashboardController extends Controller
{
    public function index(Hotel $hotel)
    {
        $counts = $this->counts($hotel);

        return view('hotel-panel.dashboard', compact('hotel', 'counts'));
    }

    public function feed(Hotel $hotel): JsonResponse
    {
        $requests = $this->activeVisibleRequests($hotel)
            ->map(function (HotelRequest $request) {
                return [
                    'id' => $request->id,
                    'point_label' => $request->point_label,
                    'type_key' => $request->type_key,
                    'type_label' => $request->typeLabel(),
                    'type_icon' => $request->typeIcon(),
                    'title' => $request->title,
                    'note' => $request->note,
                    'status' => $request->status,
                    'status_label' => $request->statusLabel(),
                    'created_at' => optional($request->created_at)->format('Y-m-d H:i:s'),
                    'created_human' => optional($request->created_at)->diffForHumans(),
                ];
            })
            ->values();

        return response()->json([
            'ok' => true,
            'counts' => $this->counts($hotel),
            'requests' => $requests,
        ]);
    }

    /**
     * Solicitudes activas visibles para recepción.
     *
     * Si existen duplicadas por doble tap/refresco, solo muestra una por:
     * hotel + punto QR + tipo + nota + estado.
     */
    private function activeVisibleRequests(Hotel $hotel)
    {
        return $this->activeRequestsQuery($hotel)
            ->latest('id')
            ->get()
            ->unique(function (HotelRequest $request) {
                return implode('|', [
                    $request->hotel_id,
                    $request->hotel_qr_point_id ?: 'no-point',
                    $request->type_key,
                    trim((string) $request->note),
                    $request->status,
                ]);
            })
            ->sortByDesc('id')
            ->take(50)
            ->values();
    }

    /**
     * Query base multi-tenant.
     *
     * No usamos $hotel->requests() con return type Builder porque eso devuelve HasMany.
     */
    private function activeRequestsQuery(Hotel $hotel): Builder
    {
        return HotelRequest::query()
            ->where('hotel_id', $hotel->id)
            ->whereIn('status', ['pending', 'in_progress']);
    }

    private function counts(Hotel $hotel): array
    {
        $today = Carbon::today();

        $activeVisible = $this->activeVisibleRequests($hotel);

        return [
            'pending' => $activeVisible
                ->where('status', 'pending')
                ->count(),

            'in_progress' => $activeVisible
                ->where('status', 'in_progress')
                ->count(),

            'completed_today' => HotelRequest::query()
                ->where('hotel_id', $hotel->id)
                ->where('status', 'completed')
                ->whereDate('completed_at', $today)
                ->count(),

            'canceled_today' => HotelRequest::query()
                ->where('hotel_id', $hotel->id)
                ->where('status', 'canceled')
                ->whereDate('canceled_at', $today)
                ->count(),
        ];
    }
}