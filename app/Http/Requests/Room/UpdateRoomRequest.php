<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;

/** Валидация обновления комнаты. */
class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.ru' => ['required', 'string', 'max:150'],
            'name.tk' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'array'],
            'description.ru' => ['nullable', 'string'],
            'description.tk' => ['nullable', 'string'],
            'category' => ['required', 'string', 'in:standard,family,vip'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'promo_price_per_hour' => ['nullable', 'numeric', 'min:0'],
            'child_price_per_hour' => ['nullable', 'numeric', 'min:0'],
            'cleaning_buffer_minutes' => ['nullable', 'integer', 'in:15,30,60,120'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:50'],
            'is_active' => ['boolean'],
            'is_walk_in' => ['sometimes', 'boolean'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
