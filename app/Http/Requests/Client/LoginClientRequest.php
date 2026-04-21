<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class LoginClientRequest extends FormRequest
{
    /** Разрешает отправку формы входа клиента. */
    public function authorize(): bool
    {
        return true;
    }

    /** Правила валидации формы входа клиента. */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+993\d{8}$/'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /** Кастомные сообщения ошибок формы входа. */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Телефон должен быть в формате +993 и 8 цифр (например, +99362724494).',
        ];
    }
}
