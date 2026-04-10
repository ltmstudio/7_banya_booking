<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessExpiredBookings extends Command
{
    protected $signature   = 'bookings:process-expired';
    protected $description = 'Cancel unconfirmed and complete finished bookings';

    public function handle(): int
    {
        $now = Carbon::now();
        $cancelled = 0;
        $completed = 0;

        // 1️⃣ Отменяем NEW брони у которых дата прошла
        Booking::where('status', 'new')
            ->where('booking_date', '<', $now->toDateString())
            ->chunkById(200, function ($bookings) use (&$cancelled, $now) {
                foreach ($bookings as $booking) {
                    $booking->update([
                        'status'        => 'cancelled',
                        'cancel_reason' => 'Auto-cancelled: booking date has passed.',
                    ]);
                    AuditLog::write('Booking', $booking->id, 'updated',
                        'status', 'new', 'cancelled');
                    $cancelled++;
                }
            });

        // 2️⃣ Завершаем PAID/CONFIRMED брони у которых прошло время уборки
        Booking::whereIn('status', ['paid', 'confirmed'])
            ->whereRaw(
                "(booking_date::date + cleaning_end_time::time)::timestamp <= ?",
                [$now]
            )
            ->chunkById(200, function ($bookings) use (&$completed) {
                foreach ($bookings as $booking) {
                    $booking->update(['status' => 'completed']);
                    AuditLog::write('Booking', $booking->id, 'updated',
                        'status', $booking->status, 'completed');
                    $completed++;
                }
            });

        $this->info("Cancelled: {$cancelled}, Completed: {$completed}");

        return self::SUCCESS;
    }
}
