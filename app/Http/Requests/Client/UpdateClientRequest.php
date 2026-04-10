<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Валидация обновления клиента. */
class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $client = $this->route('client');
        $id = $client?->id ?? $client;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+993\d{8}$/', Rule::unique('clients', 'phone')->ignore($id)],
            'birthday' => ['nullable', 'date', 'after_or_equal:1950-01-01', 'before_or_equal:'.(now()->year - 3).'-12-31'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Телефон должен быть в формате +993 и 8 цифр (например, +99361234567).',
        ];
    }
}
