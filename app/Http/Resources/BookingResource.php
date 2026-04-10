<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** API-ресурс бронирования для панели. */
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $room = $this->whenLoaded('room');
        $client = $this->whenLoaded('client');
        $extraServices = $this->whenLoaded('extraServices');

        $extraServicesArray = $extraServices
            ? $this->extraServices->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->getTranslation('name', app()->getLocale()) ?? $s->name,
                'quantity' => (int) ($s->pivot->quantity ?? 1),
                'price_at_booking' => (float) ($s->pivot->price_at_booking ?? $s->pivot->price_snapshot ?? $s->price ?? 0),
            ])->values()->all()
            : [];

        return [
            'id' => $this->id,
            'room' => $room ? (new RoomResource($this->room))->toArray($request) : null,
            'client' => $client ? (new ClientResource($this->client))->toArray($request) : null,
            'created_by' => $this->whenLoaded('createdBy', fn () => $this->createdBy?->name),
            'booking_date' => $this->booking_date?->format('d.m.Y'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'cleaning_end_time' => $this->cleaning_end_time,
            'duration_hours' => $this->duration_hours,
            'guests_count' => $this->guests_count,
            'status' => $this->status,
            'status_label' => __('booking.status_'.$this->status),
            'source' => $this->source ?? 'admin',
            'base_amount' => $this->base_amount ? (float) $this->base_amount : null,
            'final_amount' => $this->final_amount ? (float) $this->final_amount : null,
            'is_promo_applied' => (bool) ($this->is_promo_applied ?? false),
            'cancel_reason' => $this->cancel_reason,
            'extra_services' => $extraServicesArray,
            'guest_name' => $this->guest_name,
            'guest_phone' => $this->guest_phone,
            'guest_last_name' => $this->guest_last_name,
            'guest_birthday' => $this->guest_birthday?->format('d.m.Y'),
            'created_at' => $this->created_at?->format('d.m.Y H:i'),
        ];
    }
}
