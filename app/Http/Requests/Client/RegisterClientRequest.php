<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class RegisterClientRequest extends FormRequest
{
    /** Разрешает отправку формы регистрации клиента. */
    public function authorize(): bool
    {
        return true;
    }

    /** Правила валидации формы регистрации клиента. */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+993\d{8}$/', 'unique:clients,phone'],
            'birthday' => ['nullable', 'date', 'after_or_equal:1950-01-01', 'before_or_equal:'.(now()->year - 3).'-12-31'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    /** Кастомные сообщения ошибок формы регистрации. */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Телефон должен быть в формате +993 и 8 цифр (например, +99362724494).',
        ];
    }
}
