<?php

namespace App\Services;

use App\Models\Booking;

/**
 * Placeholder: интеграция с фискальным регистратором (Атол / Штрих-М и т.п.).
 * При смене статуса на «Оплачено» контроллер вызывает printReceipt().
 */
class FiscalService
{
    /** Печать чека по бронированию. Пока только логирование. */
    public function printReceipt(Booking $booking): void
    {
        // TODO: вызов драйвера кассы (Атол, Штрих-М и т.д.)
        // Пример: $this->driver->openReceipt(); $this->driver->addPosition(...); $this->driver->closeReceipt();
        logger()->info('FiscalService: печать чека для брони #'.$booking->id.', сумма '.$booking->final_amount);
    }
}
