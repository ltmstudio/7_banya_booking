<?php

namespace App\Jobs;

use App\Services\BookingCompletionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Асинхронно завершает бронирование по ID (если статус допускает завершение).
 */
class CompleteBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Повторить до 3 раз при ошибке. */
    public int $tries = 3;

    /** Таймаут выполнения (сек). */
    public int $timeout = 60;

    public function __construct(
        public int $bookingId
    ) {}

    public function handle(BookingCompletionService $service): void
    {
        $service->completeIfAllowed($this->bookingId);
    }
}
