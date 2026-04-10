<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

/** Валидация смены статуса бронирования (в т.ч. отмена с причиной). */
class UpdateBookingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:new,preliminary,confirmed,paid,completed,cancelled'],
            'cancel_reason' => ['required_if:status,cancelled', 'nullable', 'string', 'max:1000'],
            'final_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
