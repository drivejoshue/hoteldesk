<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HotelReportController extends Controller
{
    public function index(Request $request, Hotel $hotel)
    {
        [$from, $to, $range] = $this->resolveRange($request);

        $base = HotelRequest::query()
            ->where('hotel_id', $hotel->id)
            ->whereBetween('created_at', [$from, $to]);

        $total = (clone $base)->count();

        $byStatus = (clone $base)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $byType = (clone $base)
            ->select('type_key', DB::raw('COUNT(*) as total'))
            ->groupBy('type_key')
            ->orderByDesc('total')
            ->pluck('total', 'type_key');

        $byPoint = (clone $base)
            ->select('point_label', DB::raw('COUNT(*) as total'))
            ->groupBy('point_label')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $avgResolveSeconds = (clone $base)
            ->whereIn('status', ['completed', 'canceled'])
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, COALESCE(completed_at, canceled_at, updated_at))) as avg_seconds')
            ->value('avg_seconds');

        $types = config('hoteldesk.request_types', []);

        return view('hotel-panel.reports.index', compact(
            'hotel',
            'range',
            'from',
            'to',
            'total',
            'byStatus',
            'byType',
            'byPoint',
            'avgResolveSeconds',
            'types'
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