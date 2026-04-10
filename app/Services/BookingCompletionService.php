<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

/**
 * Сервис завершения бронирований: проверяет статус и пишет AuditLog.
 */
class BookingCompletionService
{
    /**
     * Завершает бронь, если она существует и находится в статусе paid/confirmed.
     */
    public function completeIfAllowed(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId) {
            /** @var Booking|null $booking */
            $booking = Booking::query()->lockForUpdate()->find($bookingId);
            if (! $booking) {
                return;
            }

            if (! in_array($booking->status, ['paid', 'confirmed'], true)) {
                return;
            }

            $oldStatus = $booking->status;
            $booking->update(['status' => 'completed']);

            AuditLog::write(
                Booking::class,
                $booking->id,
                'updated',
                'status',
                $oldStatus,
                'completed'
            );
        });
    }
}
