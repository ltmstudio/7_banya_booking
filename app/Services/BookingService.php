<?php

namespace App\Services;

use App\DTO\BookingDTO;
use App\Events\BookingCreated;
use App\Models\Booking;
use App\Models\ExtraService;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Бизнес-логика бронирований: создание, обновление, смена статуса, расчёт времени и сумм.
 */
class BookingService
{
    public function __construct(
        private FiscalService $fiscalService
    ) {}

    /** Брони на указанную дату. */
    public function getForDate(string $date): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::with(['room', 'client', 'extraServices'])
            ->whereDate('booking_date', $date)
            ->orderBy('start_time')
            ->get();
    }

    /** Брони на сегодня. */
    public function getToday(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getForDate(now()->format('Y-m-d'));
    }

    /** Пагинация списка бронирований с фильтрами. */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $q = Booking::with(['room', 'client', 'extraServices']);
        if (! empty($filters['date_from'])) {
            $q->whereDate('booking_date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $q->whereDate('booking_date', '<=', $filters['date_to']);
        }
        if (! empty($filters['date'])) {
            $q->whereDate('booking_date', $filters['date']);
        }
        if (! empty($filters['room_id'])) {
            $q->where('room_id', $filters['room_id']);
        }
        if (! empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $search = addcslashes($filters['search'], '%_\\');
            $q->where(function ($query) use ($search) {
                $query->whereHas('client', function ($q) use ($search) {
                    $q->where('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%")
                        ->orWhere('phone', 'ilike', "%{$search}%");
                })->orWhere('guest_name', 'ilike', "%{$search}%")
                    ->orWhere('guest_phone', 'ilike', "%{$search}%");
            });
        }

        return $q->orderBy('booking_date')->orderBy('start_time')->paginate($perPage);
    }

    /** Список бронирований для экспорта с теми же фильтрами, что и paginate. */
    public function getForExport(array $filters = []): \Illuminate\Support\Collection
    {
        $q = Booking::with(['room', 'client']);
        if (! empty($filters['date_from'])) {
            $q->whereDate('booking_date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $q->whereDate('booking_date', '<=', $filters['date_to']);
        }
        if (! empty($filters['date'])) {
            $q->whereDate('booking_date', $filters['date']);
        }
        if (! empty($filters['room_id'])) {
            $q->where('room_id', $filters['room_id']);
        }
        if (! empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $search = addcslashes($filters['search'], '%_\\');
            $q->where(function ($query) use ($search) {
                $query->whereHas('client', function ($q) use ($search) {
                    $q->where('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%")
                        ->orWhere('phone', 'ilike', "%{$search}%");
                })->orWhere('guest_name', 'ilike', "%{$search}%")
                    ->orWhere('guest_phone', 'ilike', "%{$search}%");
            });
        }

        return $q->orderBy('booking_date', 'desc')->orderBy('start_time', 'desc')->get();
    }

    /** Создать бронирование (админ или с сайта). */
    public function create(BookingDTO $dto, ?int $createdBy = null): Booking
    {
        $room = Room::findOrFail($dto->roomId);
        $endTime = $this->computeEndTime($dto->startTime, $dto->durationHours);
        $cleaningEnd = $this->cleaningEndTime($room, $endTime);
        $endAt = $this->computeEndAt($dto->bookingDate?->format('Y-m-d') ?? now()->format('Y-m-d'), $cleaningEnd);

        $baseAmount = $dto->baseAmount ?? $this->calculateBaseAmount($room, $dto);
        $finalAmount = $dto->finalAmount ?? $baseAmount;

        $attrs = array_merge($dto->toArray(), [
            'created_by' => $createdBy ?? auth()->id(),
            'end_time' => $endTime,
            'cleaning_end_time' => $cleaningEnd,
            'end_at' => $endAt,
            'base_amount' => $baseAmount,
            'final_amount' => $finalAmount,
        ]);
        $booking = Booking::create($attrs);

        $this->syncExtraServices($booking, $dto->extraServices);
        // Broadcast to admin panel (only for online bookings)
        if ($booking->source === 'online') {
            broadcast(new BookingCreated($booking->load('room', 'client')));
        }

        return $booking->load(['room', 'client', 'extraServices']);
    }

    /** Обновить бронирование. */
    public function update(Booking $booking, BookingDTO $dto): Booking
    {
        $attrs = $dto->toArray();
        if (! empty($dto->startTime) && ! empty($dto->durationHours)) {
            $room = $dto->roomId
                ? Room::findOrFail($dto->roomId)
                : ($booking->room ?? Room::findOrFail($booking->room_id));
            $attrs['end_time'] = $this->computeEndTime($dto->startTime, $dto->durationHours);
            $attrs['cleaning_end_time'] = $this->cleaningEndTime($room, $attrs['end_time']);
            $bookingDate = $dto->bookingDate?->format('Y-m-d') ?? $booking->booking_date?->format('Y-m-d') ?? now()->format('Y-m-d');
            $attrs['end_at'] = $this->computeEndAt($bookingDate, $attrs['cleaning_end_time']);
        }
        if (! empty($dto->bookingDate) && empty($attrs['end_at'])) {
            $bookingDate = $dto->bookingDate->format('Y-m-d');
            $cleaningEnd = $attrs['cleaning_end_time'] ?? $booking->cleaning_end_time;
            if ($cleaningEnd) {
                $attrs['end_at'] = $this->computeEndAt($bookingDate, (string) $cleaningEnd);
            }
        }
        $booking->update($attrs);
        if (array_key_exists('extra_services', $dto->toArray()) || $dto->extraServices !== []) {
            $this->syncExtraServices($booking, $dto->extraServices);
        }

        return $booking->fresh(['room', 'client', 'extraServices']);
    }

    /** Сменить статус; при оплате — печать чека (FiscalService). */
    public function updateStatus(Booking $booking, string $status, ?string $cancelReason = null): Booking
    {
        $booking->update([
            'status' => $status,
            'cancel_reason' => $status === 'cancelled' ? $cancelReason : $booking->cancel_reason,
        ]);
        if ($status === 'paid') {
            $this->fiscalService->printReceipt($booking);
        }

        return $booking->fresh();
    }

    /** Установить итоговую сумму вручную (скидка/надбавка). */
    public function setFinalAmount(Booking $booking, float $amount): Booking
    {
        $booking->update(['final_amount' => $amount]);

        return $booking->fresh();
    }

    /**
     * Проверить, свободен ли слот в комнате (дата + время + длительность).
     * При пересечении с существующей бронью (с учётом уборки) бросает \RuntimeException.
     */
    public function checkSlotAvailability(int $roomId, string $date, string $startTime, float $durationHours): void
    {
        $room = Room::find($roomId);
        if (! $room) {
            throw new \RuntimeException('Room not found');
        }
        $endTime = $this->computeEndTime($startTime, $durationHours);
        $cleaningEnd = $this->cleaningEndTime($room, $endTime);

        $overlap = Booking::where('room_id', $roomId)
            ->whereDate('booking_date', $date)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->whereRaw('(start_time < ? AND cleaning_end_time > ?)', [$cleaningEnd, $startTime])
            ->exists();

        if ($overlap) {
            throw new \RuntimeException('Slot is busy');
        }
    }

    private function computeEndTime(string $startTime, float $durationHours): string
    {
        $start = Carbon::parse($startTime);
        $totalMinutes = (int) round($durationHours * 60);

        return $start->copy()->addMinutes($totalMinutes)->format('H:i:s');
    }

    private function cleaningEndTime(Room $room, string $endTime): string
    {
        // Используем буфер уборки комнаты, если задан, иначе — глобальный из настроек
        $bufferMinutes = $room->cleaning_buffer_minutes
            ?? (int) (\App\Models\Setting::get('cleaning_buffer_minutes', 15));

        return Carbon::parse($endTime)->addMinutes($bufferMinutes)->format('H:i:s');
    }

    /**
     * Рассчитать `end_at` (timestamp): booking_date + cleaning_end_time.
     */
    private function computeEndAt(string $bookingDate, string $cleaningEndTime): Carbon
    {
        return Carbon::parse($bookingDate.' '.$cleaningEndTime);
    }

    private function calculateBaseAmount(Room $room, BookingDTO $dto): float
    {
        $pricePerHour = $dto->isPromoApplied && ($room->promo_price_per_hour ?? 0) > 0
            ? ($room->promo_price_per_hour ?? $room->price ?? 0)
            : ($room->price_per_hour ?? $room->price ?? 0);
        $total = $pricePerHour * $dto->durationHours;
        foreach ($dto->extraServices as $item) {
            $total += ($item['price_at_booking'] ?? 0) * ($item['quantity'] ?? 1);
        }

        return round($total, 2);
    }

    private function syncExtraServices(Booking $booking, array $extraServices): void
    {
        $sync = [];
        foreach ($extraServices as $extraServiceId => $item) {
            $service = ExtraService::find($extraServiceId);
            if ($service) {
                $price = $item['price_at_booking'] ?? $service->price;
                $sync[$extraServiceId] = [
                    'quantity' => $item['quantity'] ?? 1,
                    'price_at_booking' => $price,
                ];
            }
        }
        $booking->extraServices()->sync($sync);
    }
}
