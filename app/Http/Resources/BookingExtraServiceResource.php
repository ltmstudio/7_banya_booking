<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Ресурс связи брони и доп. услуги (с количеством и ценой на момент брони). */
class BookingExtraServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $pivot = $this->pivot ?? $this;
        $price = $pivot->price_at_booking ?? $pivot->price_snapshot ?? $this->price ?? 0;

        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()) ?? $this->name,
            'quantity' => (int) ($pivot->quantity ?? 1),
            'price_at_booking' => (float) $price,
        ];
    }
}
