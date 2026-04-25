<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelPinResetRequest;
use Illuminate\Http\Request;

class HotelPinResetRequestController extends Controller
{
    public function create(Hotel $hotel)
    {
        if ($hotel->status === 'disabled') {
            return view('hotel-panel.unavailable', compact('hotel'));
        }

        return view('hotel-panel.pin-reset.create', compact('hotel'));
    }

    public function store(Request $request, Hotel $hotel)
    {
        if ($hotel->status === 'disabled') {
            return view('hotel-panel.unavailable', compact('hotel'));
        }

        $data = $request->validate([
            'requester_name' => ['required', 'string', 'max:120'],
        'requester_phone' => ['required', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $alreadyPending = HotelPinResetRequest::query()
            ->where('hotel_id', $hotel->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if (! $alreadyPending) {
          HotelPinResetRequest::create([
    'hotel_id' => $hotel->id,
    'requester_name' => $data['requester_name'],
    'requester_phone' => $data['requester_phone'],
    'note' => $data['note'] ?? null,
    'status' => 'pending',
]);
        }

        return view('hotel-panel.pin-reset.sent', compact('hotel'));
    }
}