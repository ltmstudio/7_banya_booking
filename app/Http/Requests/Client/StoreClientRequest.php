<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

/** Валидация создания клиента. */
class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+993\d{8}$/', 'unique:clients,phone'],
            'birthday' => ['nullable', 'date', 'after_or_equal:1950-01-01', 'before_or_equal:'.(now()->year - 3).'-12-31'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Телефон должен быть в формате +993 и 8 цифр (например, +99362724494).',
        ];
    }
}
