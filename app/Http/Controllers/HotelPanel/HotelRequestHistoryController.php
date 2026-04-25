<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HotelRequestHistoryController extends Controller
{
    public function index(Request $request, Hotel $hotel)
    {
        [$from, $to, $range] = $this->resolveRange($request);

        $query = HotelRequest::query()
            ->where('hotel_id', $hotel->id)
            ->whereIn('status', ['completed', 'canceled'])
            ->whereBetween('created_at', [$from, $to])
            ->latest('id');

        if ($request->filled('status') && in_array($request->status, ['completed', 'canceled'], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type_key')) {
            $query->where('type_key', $request->type_key);
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->q);

            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('point_label', 'like', "%{$q}%")
                    ->orWhere('note', 'like', "%{$q}%")
                    ->orWhere('guest_name', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%");
            });
        }

        $requests = $query
            ->paginate(30)
            ->withQueryString();

        $types = config('hoteldesk.request_types', []);

        return view('hotel-panel.requests.history', compact(
            'hotel',
            'requests',
            'types',
            'range',
            'from',
            'to'
        ));
    }

    private function resolveRange(Request $request): array
    {
        $range = $request->get('range', '24h');
        $now = now();

        return match ($range) {
            '12h' => [$now->copy()->subHours(12), $now, '12h'],
            'today' => [Carbon::today(), Carbon::tomorrow()->subSecond(), 'today'],
            'yesterday' => [
                Carbon::yesterday(),
                Carbon::today()->subSecond(),
                'yesterday',
            ],
            'custom' => [
                Carbon::parse($request->get('from', $now->copy()->subDay()->format('Y-m-d')))->startOfDay(),
                Carbon::parse($request->get('to', $now->format('Y-m-d')))->endOfDay(),
                'custom',
            ],
            default => [$now->copy()->subHours(24), $now, '24h'],
        };
    }
}