<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelQrCreationRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HotelQrCreationRequestController extends Controller
{
    public function index(Hotel $hotel)
    {
        $requests = $hotel->qrCreationRequests()
            ->latest()
            ->paginate(20);

        return view('hotel-panel.qr-requests.index', compact('hotel', 'requests'));
    }

    public function create(Hotel $hotel)
    {
        $requestTypes = config('hoteldesk.request_types');

        return view('hotel-panel.qr-requests.create', compact('hotel', 'requestTypes'));
    }

    public function store(Request $request, Hotel $hotel)
    {
        $requestTypes = config('hoteldesk.request_types');
        $typeKeys = array_keys($requestTypes);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in(['room', 'lobby', 'area', 'restaurant', 'parking', 'reception', 'other'])],
            'floor' => ['nullable', 'string', 'max:30'],
            'mode' => ['required', Rule::in(['menu', 'limited', 'direct'])],
            'fixed_request_type' => ['nullable', 'string', Rule::in($typeKeys)],
            'allowed_request_types' => ['nullable', 'array'],
            'allowed_request_types.*' => ['string', Rule::in($typeKeys)],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $mode = $data['mode'];

        if ($mode === 'direct' && empty($data['fixed_request_type'])) {
            return back()
                ->withErrors(['fixed_request_type' => 'Selecciona el tipo de solicitud directa.'])
                ->withInput();
        }

        if ($mode === 'limited' && empty($data['allowed_request_types'])) {
            return back()
                ->withErrors(['allowed_request_types' => 'Selecciona al menos un tipo de solicitud permitida.'])
                ->withInput();
        }

        HotelQrCreationRequest::create([
            'hotel_id' => $hotel->id,
            'label' => trim($data['label']),
            'type' => $data['type'],
            'floor' => $data['floor'] ?? null,
            'mode' => $mode,
            'fixed_request_type' => $mode === 'direct' ? $data['fixed_request_type'] : null,
            'allowed_request_types' => $mode === 'limited' ? array_values($data['allowed_request_types']) : null,
            'note' => $data['note'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('hotel.qr-requests.index', $hotel)
            ->with('success', 'Solicitud enviada. SysApp revisará la creación del QR.');
    }
}