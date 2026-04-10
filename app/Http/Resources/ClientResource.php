<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** API-ресурс клиента: id, имя, телефон, ДР, именинник, кол-во броней. */
class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $birthday = $this->birthday;
        $fullName = trim($this->first_name.' '.($this->last_name ?? ''));

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $fullName ?: $this->first_name,
            'phone' => $this->phone,
            'birthday' => $birthday?->format('d.m.Y') ?? null,
            'is_birthday_today' => $birthday ? ($birthday->isToday()) : false,
            'bookings_count' => $this->whenCounted('bookings'),
            'created_at' => $this->created_at?->format('d.m.Y'),
        ];
    }
}
