<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\ClientResource;
use App\Models\Booking;
use App\Models\Client;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

/** Дашборд админки: заявки на сегодня, именинники, быстрые действия. */
class DashboardController extends Controller
{
    public function index(): Response
    {
        // Брони сегодня
        $todayBookings = Booking::with('room', 'client')
            ->today()
            ->orderBy('start_time')
            ->get();

        // Именинники сегодня
        $birthdayClients = Client::todayBirthday()
            ->withCount('bookings')
            ->get();

        // Для мини-календаря: количество броней по датам текущего месяца
        $bookingsByDate = Booking::selectRaw('booking_date, COUNT(*) as count')
            ->whereYear('booking_date', now()->year)
            ->whereMonth('booking_date', now()->month)
            ->whereNotIn('status', ['cancelled'])
            ->groupBy('booking_date')
            ->pluck('count', 'booking_date')
            ->mapWithKeys(fn ($count, $date) => [
                Carbon::parse($date)->format('Y-m-d') => (int) $count,
            ])
            ->toArray();

        // Статистика
        $stats = [
            'today_count' => (int) $todayBookings->count(),
            'today_revenue' => (float) $todayBookings
                ->whereIn('status', ['paid', 'completed'])
                ->sum('final_amount'),
            'month_revenue' => (float) Booking::whereMonth('booking_date', now()->month)
                ->whereIn('status', ['paid', 'completed'])
                ->sum('final_amount'),
            'total_clients' => (int) Client::count(),
        ];

        $todayBookingsData = $todayBookings
            ->map(fn ($b) => (new BookingResource($b))->toArray(request()))
            ->values()
            ->all();

        $birthdayClientsData = $birthdayClients
            ->map(fn ($c) => (new ClientResource($c))->toArray(request()))
            ->values()
            ->all();

        return Inertia::render('Panel/Dashboard/Index', [
            'todayBookings' => $todayBookingsData,
            'birthdayClients' => $birthdayClientsData,
            'bookingsByDate' => $bookingsByDate,
            'stats' => $stats,
        ]);
    }
}
