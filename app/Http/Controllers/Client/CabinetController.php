<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateCabinetPasswordRequest;
use App\Http\Requests\Client\UpdateCabinetProfileRequest;
use App\Services\ClientCabinetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class CabinetController extends Controller
{
    public function __construct(private ClientCabinetService $clientCabinetService) {}

    /** Главная страница личного кабинета клиента. */
    public function index(): Response
    {
        $client = Auth::guard('client')->user();

        return Inertia::render('Client/Cabinet/Index', $this->clientCabinetService->buildCabinetData($client));
    }

    /** Обновляет профиль клиента в личном кабинете. */
    public function updateProfile(UpdateCabinetProfileRequest $request): RedirectResponse
    {
        $client = Auth::guard('client')->user();
        $this->clientCabinetService->updateProfile($client, $request->validated());

        return back()->with('success', true);
    }

    /** Обновляет пароль клиента в личном кабинете. */
    public function updatePassword(UpdateCabinetPasswordRequest $request): RedirectResponse
    {
        $client = Auth::guard('client')->user();
        if (! Hash::check($request->validated('current_password'), (string) $client->password)) {
            return back()->withErrors(['current_password' => 'Неверный текущий пароль']);
        }

        $this->clientCabinetService->updatePassword($client, $request->validated('password'));

        return back()->with('success', true);
    }
}
