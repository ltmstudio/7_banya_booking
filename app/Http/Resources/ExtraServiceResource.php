<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** API-ресурс доп. услуги. */
class ExtraServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()) ?? $this->name,
            'icon_path' => $this->icon_path ?? $this->icon ?? null,
            'description' => $this->getTranslation('description', app()->getLocale()) ?? $this->description,
            'price' => $this->price,
            'is_active' => $this->is_active ?? true,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
