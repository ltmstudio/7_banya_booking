<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** API-ресурс комнаты. */
class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $name = $this->name;
        $description = $this->description;
        if (is_string($name)) {
            $name = json_decode($name, true) ?? ['ru' => $name, 'tk' => $name];
        }
        if (is_string($description)) {
            $description = json_decode($description, true) ?? ['ru' => $description, 'tk' => $description];
        }

        return [
            'id' => $this->id,
            'name' => $name ?? ['ru' => '', 'tk' => ''],
            'description' => $description ?? ['ru' => '', 'tk' => ''],
            'category' => $this->category,
            'category_label' => match ($this->category) {
                'standard' => __('room.category_standard'),
                'family' => __('room.category_family'),
                'vip' => __('room.category_vip'),
                default => $this->category,
            },
            'price_per_hour' => (float) $this->price_per_hour,
            'promo_price_per_hour' => $this->promo_price_per_hour ? (float) $this->promo_price_per_hour : null,
            'child_price_per_hour' => $this->child_price_per_hour,
            'cleaning_buffer_minutes' => $this->cleaning_buffer_minutes,
            'capacity' => $this->capacity,
            'is_active' => (bool) ($this->is_active ?? true),
            'is_walk_in' => (bool) ($this->is_walk_in ?? false),
            'photos' => $this->whenLoaded('photos', fn () => $this->photos->map(fn ($p) => [
                'id' => $p->id,
                'url' => Storage::url($p->path),
                'path' => $p->path,
                'sort_order' => $p->sort_order,
            ])->values()->all()),
            'created_at' => $this->created_at?->format('d.m.Y'),
        ];
    }
}
