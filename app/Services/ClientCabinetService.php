<?php

namespace App\Services;

use App\Http\Resources\BookingResource;
use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Бизнес-логика личного кабинета клиента.
 */
class ClientCabinetService
{
    /** Формирует пропсы клиента и статистику для страницы кабинета. */
    public function buildCabinetData(Client $client): array
    {
        $bookings = $client->bookings()
            ->with(['room.photos', 'extraServices'])
            ->paginate(10);

        return [
            'client' => [
                'id' => $client->id,
                'full_name' => $client->full_name,
                'first_name' => $client->first_name,
                'last_name' => $client->last_name,
                'phone' => $client->phone,
                'birthday' => $client->birthday?->format('Y-m-d'),
                'is_birthday_today' => $client->birthday?->format('m-d') === now()->format('m-d'),
            ],
            'bookings' => $this->bookingCollection($bookings),
            'stats' => [
                'total' => $client->bookings()->count(),
                'completed' => $client->bookings()->where('status', 'completed')->count(),
                'upcoming' => $client->bookings()
                    ->whereIn('status', ['new', 'preliminary', 'confirmed'])
                    ->whereDate('booking_date', '>=', today())
                    ->count(),
            ],
        ];
    }

    /** Обновляет профиль клиента в кабинете. */
    public function updateProfile(Client $client, array $data): Client
    {
        $client->update($data);

        return $client->fresh();
    }

    /** Обновляет пароль клиента. */
    public function updatePassword(Client $client, string $password): Client
    {
        $client->update(['password' => $password]);

        return $client->fresh();
    }

    /** Преобразует пагинацию броней в формат, удобный для Inertia-страницы. */
    private function bookingCollection(LengthAwarePaginator $bookings): array
    {
        $data = BookingResource::collection($bookings)->response()->getData(true);

        return [
            'data' => $data['data'] ?? [],
            'meta' => $data['meta'] ?? [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
            'links' => $data['links'] ?? [],
        ];
    }
}
