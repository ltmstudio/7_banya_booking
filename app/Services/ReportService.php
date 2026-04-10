<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Отчёты: заработок за период, по комнатам, по доп. услугам, экспорт в Excel (placeholder).
 */
class ReportService
{
    /** Заработок за период (итого по final_amount). */
    public function earningsForPeriod(Carbon $from, Carbon $to, ?int $roomId = null): float
    {
        $q = Booking::query()
            ->whereIn('status', ['paid', 'completed'])
            ->whereDate('booking_date', '>=', $from)
            ->whereDate('booking_date', '<=', $to);
        if ($roomId !== null) {
            $q->where('room_id', $roomId);
        }

        return (float) $q->sum('final_amount');
    }

    /** Разбивка по комнатам за период. */
    public function earningsByRoom(Carbon $from, Carbon $to): array
    {
        return Booking::query()
            ->select('room_id', DB::raw('SUM(final_amount) as total'))
            ->whereIn('status', ['paid', 'completed'])
            ->whereDate('booking_date', '>=', $from)
            ->whereDate('booking_date', '<=', $to)
            ->groupBy('room_id')
            ->with('room:id,name')
            ->get()
            ->map(fn ($row) => [
                'room_id' => $row->room_id,
                'room_name' => $row->room?->name,
                'total' => (float) $row->total,
            ])
            ->toArray();
    }

    /** Заработок только с доп. услуг за период (из pivot). */
    public function earningsExtraServices(Carbon $from, Carbon $to): float
    {
        return (float) DB::table('booking_extra_service')
            ->join('bookings', 'bookings.id', '=', 'booking_extra_service.booking_id')
            ->whereIn('bookings.status', ['paid', 'completed'])
            ->whereDate('bookings.booking_date', '>=', $from)
            ->whereDate('bookings.booking_date', '<=', $to)
            ->selectRaw('SUM(booking_extra_service.price_at_booking * booking_extra_service.quantity) as total')
            ->value('total') ?? 0;
    }

    /** Экспорт отчёта в Excel — placeholder. */
    public function exportToExcel(Carbon $from, Carbon $to): string
    {
        // TODO: интеграция с maatwebsite/excel или аналогичным пакетом
        return storage_path('app/reports/earnings_'.$from->format('Y-m-d').'_'.$to->format('Y-m-d').'.xlsx');
    }
}
