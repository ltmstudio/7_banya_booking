<?php

namespace App\Services;

use App\DTO\ClientDTO;
use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Бизнес-логика клиентов: создание, обновление, поиск, именинники.
 */
class ClientService
{
    /** Список с пагинацией и поиском по имени/телефону. */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = Client::query()
            ->withCount('bookings')
            ->orderBy('first_name');
        if ($search !== null && $search !== '') {
            $search = addcslashes($search, '%_\\');
            $q->where(function ($query) use ($search) {
                $query->where('first_name', 'ilike', "%{$search}%")
                    ->orWhere('last_name', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        return $q->paginate($perPage);
    }

    /** Клиенты с ДР сегодня. */
    public function getTodayBirthday(): \Illuminate\Database\Eloquent\Collection
    {
        return Client::query()->todayBirthday()->orderBy('first_name')->get();
    }

    /** Найти или создать по телефону (для гостевых броней). */
    public function firstOrCreateByPhone(string $phone, array $attributes = []): Client
    {
        return Client::firstOrCreate(
            ['phone' => $phone],
            array_merge($attributes, ['first_name' => $attributes['first_name'] ?? 'Гость'])
        );
    }

    /** Создать клиента. */
    public function create(ClientDTO $dto): Client
    {
        return Client::create($dto->toArray());
    }

    /** Обновить клиента. */
    public function update(Client $client, ClientDTO $dto): Client
    {
        $client->update($dto->toArray());

        return $client->fresh();
    }
}
