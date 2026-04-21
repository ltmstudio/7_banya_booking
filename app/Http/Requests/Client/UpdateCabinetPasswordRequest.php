<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCabinetPasswordRequest extends FormRequest
{
    /** Разрешает обновление пароля авторизованного клиента. */
    public function authorize(): bool
    {
        return true;
    }

    /** Правила валидации обновления пароля в кабинете. */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }
}
