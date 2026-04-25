<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelQrPoint;
use App\Models\HotelRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PublicQrRequestController extends Controller
{
    private const DUPLICATE_WINDOW_SECONDS = 90;

    private const NOTE_REQUIRED_TYPES = [
        'suggestion',
        'other',
    ];

    public function show(string $code)
    {
        $point = $this->findActivePointByCode($code);
        $hotel = $point->hotel;

        if (! $this->isPointAvailable($point)) {
            return view('public-hotel.unavailable', compact('hotel', 'point'));
        }

        $types = $this->availableTypesForPoint($point);

        if (empty($types)) {
            return view('public-hotel.unavailable', compact('hotel', 'point'));
        }

        return view('public-hotel.request', compact('hotel', 'point', 'types'));
    }

    public function store(Request $request, string $code)
    {
        $point = $this->findActivePointByCode($code);
        $hotel = $point->hotel;

        if (! $this->isPointAvailable($point)) {
            return view('public-hotel.unavailable', compact('hotel', 'point'));
        }

        $types = $this->availableTypesForPoint($point);

        if (empty($types)) {
            return view('public-hotel.unavailable', compact('hotel', 'point'));
        }

        $data = $this->validateRequest($request, $types);

        $typeKey = $data['type_key'];
        $type = $types[$typeKey];

        $note = $this->normalizeNullableText($data['note'] ?? null);
        $guestName = $this->normalizeNullableText($data['guest_name'] ?? null);

        if ($this->requiresNote($typeKey) && $note === null) {
            return back()
                ->withErrors([
                    'note' => $this->requiredNoteMessage($typeKey),
                ])
                ->withInput();
        }

        $existingRequest = $this->findRecentDuplicate(
            hotel: $hotel,
            point: $point,
            typeKey: $typeKey,
            note: $note,
            seconds: self::DUPLICATE_WINDOW_SECONDS
        );

        if ($existingRequest) {
            $hotelRequest = $existingRequest;

            return view('public-hotel.sent', compact('hotel', 'point', 'hotelRequest'));
        }

        $hotelRequest = HotelRequest::create([
            'hotel_id' => $hotel->id,
            'hotel_qr_point_id' => $point->id,
            'point_label' => $point->label,
            'type_key' => $typeKey,
            'title' => $type['label'] ?? Str::headline($typeKey),
            'note' => $note,
            'guest_name' => $guestName,
            'status' => 'pending',
            'source' => 'qr',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);

        return view('public-hotel.sent', compact('hotel', 'point', 'hotelRequest'));
    }

    private function findActivePointByCode(string $code): HotelQrPoint
    {
        return HotelQrPoint::query()
            ->with('hotel')
            ->where('public_code', $code)
            ->where('active', true)
            ->firstOrFail();
    }

    private function isPointAvailable(HotelQrPoint $point): bool
    {
        $hotel = $point->hotel;

        return $hotel instanceof Hotel
            && $hotel->isPublicAvailable()
            && $point->active;
    }

    private function availableTypesForPoint(HotelQrPoint $point): array
    {
        $hotel = $point->hotel;
        $types = $point->availableRequestTypes();

        if (! is_array($types)) {
            return [];
        }

        if (! $hotel || ! $hotel->taxi_enabled) {
            unset($types['taxi']);
        }

        return $types;
    }

    private function validateRequest(Request $request, array $types): array
    {
        $validTypeKeys = array_keys($types);

        abort_if(empty($validTypeKeys), 404);

        return $request->validate([
            'type_key' => [
                'required',
                'string',
                Rule::in($validTypeKeys),
            ],
            'note' => [
                'nullable',
                'string',
                'max:500',
            ],
            'guest_name' => [
                'nullable',
                'string',
                'max:120',
            ],
        ], [
            'type_key.required' => 'Selecciona una opción para continuar.',
            'type_key.in' => 'La opción seleccionada no está disponible.',
            'note.max' => 'La nota no puede superar 500 caracteres.',
            'guest_name.max' => 'El nombre no puede superar 120 caracteres.',
        ]);
    }

    private function findRecentDuplicate(
        Hotel $hotel,
        HotelQrPoint $point,
        string $typeKey,
        ?string $note,
        int $seconds = self::DUPLICATE_WINDOW_SECONDS
    ): ?HotelRequest {
        return HotelRequest::query()
            ->where('hotel_id', $hotel->id)
            ->where('hotel_qr_point_id', $point->id)
            ->where('type_key', $typeKey)
            ->whereIn('status', ['pending', 'in_progress'])
            ->where('created_at', '>=', now()->subSeconds($seconds))
            ->where(function (Builder $query) use ($note) {
                $this->whereSameNullableText($query, 'note', $note);
            })
            ->latest('id')
            ->first();
    }

    private function whereSameNullableText(Builder $query, string $column, ?string $value): void
    {
        if ($value === null) {
            $query->where(function (Builder $subQuery) use ($column) {
                $subQuery->whereNull($column)
                    ->orWhere($column, '');
            });

            return;
        }

        $query->where($column, $value);
    }

    private function normalizeNullableText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(strip_tags($value));

        return $value === '' ? null : $value;
    }

    private function requiresNote(string $typeKey): bool
    {
        return in_array($typeKey, self::NOTE_REQUIRED_TYPES, true);
    }

    private function requiredNoteMessage(string $typeKey): string
    {
        return match ($typeKey) {
            'suggestion' => 'Escribe tu sugerencia antes de enviarla.',
            'other' => 'Describe tu solicitud antes de enviarla.',
            default => 'Escribe el detalle antes de enviar la solicitud.',
        };
    }
}