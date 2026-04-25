<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

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
            ->map(fn (HotelRequest $request) => $this->formatRequest($request))
            ->values();

        return response()->json([
            'ok' => true,
            'counts' => $this->counts($hotel),
            'requests' => $requests,
        ]);
    }

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

    private function activeRequestsQuery(Hotel $hotel): Builder
    {
        return HotelRequest::query()
            ->where('hotel_id', $hotel->id)
            ->whereIn('status', ['pending', 'in_progress']);
    }

    private function counts(Hotel $hotel): array
    {
        $activeVisible = $this->activeVisibleRequests($hotel);

        $from24h = now()->subHours(24);

        return [
            'pending' => $activeVisible
                ->where('status', 'pending')
                ->count(),

            'in_progress' => $activeVisible
                ->where('status', 'in_progress')
                ->count(),

            // Operativo: últimas 24 horas, no "hoy calendario".
            'completed_today' => HotelRequest::query()
                ->where('hotel_id', $hotel->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->where('completed_at', '>=', $from24h)
                ->count(),

            'canceled_today' => HotelRequest::query()
                ->where('hotel_id', $hotel->id)
                ->where('status', 'canceled')
                ->whereNotNull('canceled_at')
                ->where('canceled_at', '>=', $from24h)
                ->count(),
        ];
    }

    private function formatRequest(HotelRequest $request): array
    {
        $createdAt = $request->created_at;

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

            'created_at' => optional($createdAt)->format('Y-m-d H:i:s'),
            'created_human' => optional($createdAt)->diffForHumans(),
            'created_clock' => optional($createdAt)->format('H:i'),
            'created_short' => optional($createdAt)->isToday()
                ? optional($createdAt)->format('H:i')
                : optional($createdAt)->format('d/m/Y H:i'),
        ];
    }
}