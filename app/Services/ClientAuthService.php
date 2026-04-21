<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Бизнес-логика аутентификации клиента (вход/регистрация/выход).
 */
class ClientAuthService
{
    /**
     * Выполняет вход клиента по телефону и паролю.
     *
     * @return array{ok: bool, error_key?: string, error_message?: string}
     */
    public function login(string $phone, string $password, bool $remember = false): array
    {
        $client = Client::query()->where('phone', $phone)->first();
        if (! $client || ! $client->password) {
            return [
                'ok' => false,
                'error_key' => 'phone',
                'error_message' => 'Клиент не найден или не зарегистрирован.',
            ];
        }

        if (! Hash::check($password, $client->password)) {
            return [
                'ok' => false,
                'error_key' => 'password',
                'error_message' => 'Неверный пароль.',
            ];
        }

        Auth::guard('client')->login($client, $remember);

        return ['ok' => true];
    }

    /** Регистрирует нового клиента и сразу авторизует его. */
    public function register(array $data): Client
    {
        $client = Client::query()->create($data);
        Auth::guard('client')->login($client);

        return $client;
    }

    /** Выход текущего клиента. */
    public function logout(): void
    {
        Auth::guard('client')->logout();
    }
}
