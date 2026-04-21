<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\LoginClientRequest;
use App\Http\Requests\Client\RegisterClientRequest;
use App\Services\ClientAuthService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function __construct(private ClientAuthService $clientAuthService) {}

    /** Страница входа клиента. */
    public function showLogin(): Response
    {
        return Inertia::render('Client/Auth/Login');
    }

    /** Обрабатывает вход клиента по телефону и паролю. */
    public function login(LoginClientRequest $request): RedirectResponse
    {
        $result = $this->clientAuthService->login(
            $request->validated('phone'),
            $request->validated('password'),
            (bool) $request->validated('remember')
        );

        if (! $result['ok']) {
            return back()->withErrors([
                $result['error_key'] => $result['error_message'],
            ])->withInput();
        }

        return redirect()->route('client.cabinet');
    }

    /** Страница регистрации клиента. */
    public function showRegister(): Response
    {
        return Inertia::render('Client/Auth/Register');
    }

    /** Регистрирует нового клиента и выполняет вход. */
    public function register(RegisterClientRequest $request): RedirectResponse
    {
        $this->clientAuthService->register($request->validated());

        return redirect()->route('client.cabinet');
    }

    /** Выход клиента из кабинета. */
    public function logout(): RedirectResponse
    {
        $this->clientAuthService->logout();

        return redirect()->route('client.login');
    }
}
