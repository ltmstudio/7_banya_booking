<?php

namespace App\DTO;

/**
 * DTO для данных комнаты (создание/обновление).
 */
final class RoomDTO
{
    public function __construct(
        public ?string $name,
        public ?string $category,
        public ?string $description,
        public ?float $pricePerHour,
        public ?float $promoPricePerHour,
        public bool $isActive = true,
    ) {}

    /** Собрать DTO из массива (request). */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            category: $data['category'] ?? null,
            description: $data['description'] ?? null,
            pricePerHour: isset($data['price_per_hour']) ? (float) $data['price_per_hour'] : null,
            promoPricePerHour: isset($data['promo_price_per_hour']) ? (float) $data['promo_price_per_hour'] : null,
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }

    /** Преобразовать в массив для модели. */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description,
            'price_per_hour' => $this->pricePerHour,
            'promo_price_per_hour' => $this->promoPricePerHour,
            'is_active' => $this->isActive,
        ], fn ($v) => $v !== null);
    }
}
