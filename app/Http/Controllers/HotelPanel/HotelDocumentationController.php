<?php

namespace App\Http\Controllers\HotelPanel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\View\View;

class HotelDocumentationController extends Controller
{
    public function index(Hotel $hotel): View
    {
        return view('hotel-panel.documentation.index', [
            'hotel' => $hotel,
        ]);
    }
}