<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\HotelDesk\HotelLicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelTrialTermsController extends Controller
{
    public function show(Hotel $hotel): View|RedirectResponse
    {
        if ($hotel->isLicenseActive()) {
            return redirect()->route('hotel.dashboard', $hotel);
        }

        if (! $hotel->isTrialPending()) {
            return redirect()->route('hotel.license.index', $hotel);
        }

        return view('hotel-panel.terms.trial', [
            'hotel' => $hotel,
        ]);
    }

    public function accept(
        Request $request,
        Hotel $hotel,
        HotelLicenseService $licenses
    ): RedirectResponse {
        if (! $hotel->isTrialPending()) {
            return redirect()->route('hotel.license.index', $hotel);
        }

        $data = $request->validate([
            'accepted_by' => ['nullable', 'string', 'max:120'],
            'accept_terms' => ['accepted'],
        ]);

        $licenses->acceptTermsAndStartTrial(
            hotel: $hotel,
            request: $request,
            acceptedBy: $data['accepted_by'] ?? null,
        );

        return redirect()
            ->route('hotel.dashboard', $hotel)
            ->with('success', 'Periodo de prueba activado correctamente.');
    }
}