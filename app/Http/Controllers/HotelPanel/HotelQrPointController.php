<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelQrPoint;
use Illuminate\Http\Request;
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
}