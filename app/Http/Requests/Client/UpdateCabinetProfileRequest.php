<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCabinetProfileRequest extends FormRequest
{
    /** Разрешает обновление профиля авторизованного клиента. */
    public function authorize(): bool
    {
        return true;
    }

    /** Правила валидации обновления профиля в кабинете. */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'birthday' => ['nullable', 'date', 'after_or_equal:1950-01-01', 'before_or_equal:'.(now()->year - 3).'-12-31'],
        ];
    }
}
