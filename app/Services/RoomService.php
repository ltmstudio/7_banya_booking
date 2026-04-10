<?php

namespace App\Services;

use App\DTO\RoomDTO;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Бизнес-логика комнат: создание, обновление, список, отключение с отменой броней.
 */
class RoomService
{
    /** Список комнат с пагинацией. */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Room::with('photos')->orderBy('id')->paginate($perPage);
    }

    /** Все активные комнаты (для выбора на сайте). */
    public function getActive(): Collection
    {
        return Room::with('photos')
            ->where($this->activeColumn(), true)
            ->orderBy('id')
            ->get();
    }

    /** Создать комнату. */
    public function create(RoomDTO $dto): Room
    {
        $attrs = $this->dtoToAttributes($dto);

        return Room::create($attrs);
    }

    /** Обновить комнату. */
    public function update(Room $room, RoomDTO $dto): Room
    {
        $attrs = $this->dtoToAttributes($dto);
        $room->update($attrs);

        return $room->fresh();
    }

    /** Отключить комнату, при необходимости отменить активные брони. */
    public function deactivate(Room $room, bool $cancelBookings = false): void
    {
        if ($cancelBookings) {
            Booking::where('room_id', $room->id)->whereNotIn('status', ['cancelled', 'completed'])->update([
                'status' => 'cancelled',
                'cancel_reason' => 'Комната отключена',
            ]);
        }
        $room->update([$this->activeColumn() => false]);
    }

    /** Преобразование DTO в атрибуты модели (текущий fillable: name, description, price, status). */
    private function dtoToAttributes(RoomDTO $dto): array
    {
        $room = new Room;
        $attrs = [];
        if ($dto->name !== null) {
            $attrs['name'] = $dto->name;
        }
        if ($dto->description !== null) {
            $attrs['description'] = $dto->description;
        }
        if ($dto->pricePerHour !== null) {
            $attrs['price'] = $dto->pricePerHour;
        }
        if (in_array('category', $room->getFillable())) {
            if ($dto->category !== null) {
                $attrs['category'] = $dto->category;
            }
        }
        if (in_array('price_per_hour', $room->getFillable())) {
            if ($dto->pricePerHour !== null) {
                $attrs['price_per_hour'] = $dto->pricePerHour;
            }
            if ($dto->promoPricePerHour !== null) {
                $attrs['promo_price_per_hour'] = $dto->promoPricePerHour;
            }
        }
        if (in_array('is_active', $room->getFillable())) {
            $attrs['is_active'] = $dto->isActive;
        }
        if (in_array('status', $room->getFillable())) {
            $attrs['status'] = $dto->isActive ? 'active' : 'inactive';
        }

        return $attrs;
    }

    private function activeColumn(): string
    {
        return in_array('is_active', (new Room)->getFillable()) ? 'is_active' : 'status';
    }
}
