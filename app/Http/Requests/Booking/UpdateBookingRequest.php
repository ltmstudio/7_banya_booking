<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

/** Валидация обновления бронирования. */
class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'exists:rooms,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'booking_date' => ['nullable', 'date'],
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
