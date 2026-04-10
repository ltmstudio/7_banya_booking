<?php

namespace App\DTO;

use Carbon\Carbon;

/**
 * DTO для данных клиента (создание/обновление).
 */
final class ClientDTO
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public ?string $phone,
        public ?Carbon $birthday = null,
    ) {}

    /** Собрать DTO из массива. */
    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['first_name'] ?? null,
            lastName: ! empty($data['last_name']) ? $data['last_name'] : null,
            phone: $data['phone'] ?? null,
            birthday: ! empty($data['birthday']) ? Carbon::parse($data['birthday']) : null,
        );
    }

    /** Массив для create/update (null для nullable полей). */
    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'birthday' => $this->birthday?->format('Y-m-d'),
        ];
    }
}
