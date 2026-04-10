<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Валидация создания бронирования (админ или публичная форма). */
class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roomIdRules = ['required', 'exists:rooms,id'];
        if ($this->routeIs('public.booking.store')) {
            $roomIdRules[] = Rule::exists('rooms', 'id')->where('is_walk_in', false);
        }

        return [
            'room_id' => $roomIdRules,
            'client_id' => ['nullable', 'exists:clients,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_hours' => ['required', 'numeric', 'min:1', 'max:10'],
            'guests_count' => ['required', 'integer', 'min:1'],
            'extra_service_ids' => ['nullable', 'array'],
            'extra_service_ids.*' => ['exists:extra_services,id'],
            'guest_name' => ['nullable', 'string', 'max:150'],
            'guest_phone' => ['nullable', 'string', 'max:20'],
            'guest_last_name' => ['nullable', 'string', 'max:100'],
            'guest_birthday' => ['nullable', 'date'],
        ];
    }
}
