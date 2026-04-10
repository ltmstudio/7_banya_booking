<?php

namespace App\Http\Controllers\Admin;

use App\DTO\ClientDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\AuditLog;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** CRUD клиентов в админке. */
class ClientController extends Controller
{
    public function __construct(private ClientService $clientService) {}

    /** Список клиентов с поиском по имени/телефону. */
    public function index(Request $request): Response
    {
        $search = $request->get('search', '');
        $clients = $this->clientService->paginate(
            (int) $request->get('per_page', 15),
            $search ?: null
        );
        $clientsData = $clients->getCollection()
            ->map(fn ($c) => (new ClientResource($c))->toArray(request()))
            ->values()
            ->all();

        return Inertia::render('Panel/Clients/Index', [
            'clients' => $clientsData,
            'meta' => ['current_page' => $clients->currentPage(), 'last_page' => $clients->lastPage(), 'total' => $clients->total()],
            'search' => $search,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Panel/Clients/Create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = $this->clientService->create(ClientDTO::fromArray($request->validated()));
        AuditLog::write(Client::class, $client->id, 'create');

        return redirect()->route('panel.clients.index')->with('success', __('client.created'));
    }

    public function show(Client $client): RedirectResponse
    {
        return redirect()->route('panel.clients.edit', $client);
    }

    public function edit(Client $client): Response
    {
        $client->loadCount('bookings');

        return Inertia::render('Panel/Clients/Edit', [
            'editingClient' => (new ClientResource($client))->toArray(request()),
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $this->clientService->update($client, ClientDTO::fromArray($request->validated()));
        AuditLog::write(Client::class, $client->id, 'update', 'fields', null, '[updated]');

        return redirect()->route('panel.clients.index')->with('success', __('client.updated'));
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();
        AuditLog::write(Client::class, $client->id, 'delete');

        return redirect()->route('panel.clients.index')->with('success', __('client.deleted'));
    }
}
