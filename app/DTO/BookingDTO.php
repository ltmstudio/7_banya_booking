<?php

namespace App\DTO;

use Carbon\Carbon;

/**
 * DTO для данных бронирования (создание/обновление).
 */
final class BookingDTO
{
    public function __construct(
        public ?int $roomId,
        public ?int $clientId,
        public ?Carbon $bookingDate,
        public ?string $startTime,
        public ?float $durationHours,
        public ?int $guestsCount,
        public ?string $status = 'new',
        public ?string $source = 'admin',
        public ?string $cancelReason = null,
        public ?float $baseAmount = null,
        public ?float $finalAmount = null,
        public bool $isPromoApplied = false,
        public ?string $guestName = null,
        public ?string $guestPhone = null,
        public ?string $guestLastName = null,
        public ?Carbon $guestBirthday = null,
        /** @var array<int, array{quantity: int, price_at_booking: float}> */
        public array $extraServices = [],
    ) {}

    /** Собрать DTO из массива. */
    public static function fromArray(array $data): self
    {
        $extraServices = [];
        if (! empty($data['extra_services'])) {
            foreach ($data['extra_services'] as $item) {
                $id = (int) ($item['extra_service_id'] ?? $item['id'] ?? 0);
                if ($id) {
                    $extraServices[$id] = [
                        'quantity' => (int) ($item['quantity'] ?? 1),
                        'price_at_booking' => (float) ($item['price_at_booking'] ?? 0),
                    ];
                }
            }
        }

        return new self(
            roomId: ! empty($data['room_id']) ? (int) $data['room_id'] : null,
            clientId: ! empty($data['client_id']) ? (int) $data['client_id'] : null,
            bookingDate: isset($data['booking_date']) ? Carbon::parse($data['booking_date']) : null,
            startTime: $data['start_time'] ?? null,
            durationHours: isset($data['duration_hours']) ? (float) $data['duration_hours'] : null,
            guestsCount: isset($data['guests_count']) ? (int) $data['guests_count'] : null,
            status: $data['status'] ?? 'new',
            source: $data['source'] ?? 'admin',
            cancelReason: $data['cancel_reason'] ?? null,
            baseAmount: isset($data['base_amount']) ? (float) $data['base_amount'] : null,
            finalAmount: isset($data['final_amount']) ? (float) $data['final_amount'] : null,
            isPromoApplied: (bool) ($data['is_promo_applied'] ?? false),
            guestName: $data['guest_name'] ?? null,
            guestPhone: $data['guest_phone'] ?? null,
            guestLastName: $data['guest_last_name'] ?? null,
            guestBirthday: isset($data['guest_birthday']) ? Carbon::parse($data['guest_birthday']) : null,
            extraServices: $extraServices,
        );
    }

    public function toArray(): array
    {
        $arr = array_filter([
            'room_id' => $this->roomId,
            'client_id' => $this->clientId,
            'booking_date' => $this->bookingDate?->format('Y-m-d'),
            'start_time' => $this->startTime,
            'duration_hours' => $this->durationHours,
            'guests_count' => $this->guestsCount,
            'status' => $this->status,
            'source' => $this->source,
            'cancel_reason' => $this->cancelReason,
            'base_amount' => $this->baseAmount,
            'final_amount' => $this->finalAmount,
            'is_promo_applied' => $this->isPromoApplied,
            'guest_name' => $this->guestName,
            'guest_phone' => $this->guestPhone,
            'guest_last_name' => $this->guestLastName,
            'guest_birthday' => $this->guestBirthday?->format('Y-m-d'),
        ]);

        return $arr;
    }
}
