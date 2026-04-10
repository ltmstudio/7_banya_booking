<?php

namespace App\Http\Controllers\Public;

use App\DTO\BookingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\ExtraService;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Публичное создание бронирования с сайта (источник online). */
class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    /** Форма бронирования. */
    public function create(): Response
    {
        return Inertia::render('Booking/Create');
    }

    /** Проверка доступности слотов по дате/времени/длительности для всех активных комнат. */
    public function checkAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_hours' => ['required', 'numeric', 'min:1', 'max:10'],
        ]);
        $date = $validated['date'];
        $startTime = $validated['start_time'];
        $durationHours = (float) $validated['duration_hours'];

        $rooms = Room::where('is_active', true)->where('is_walk_in', false)->get();
        $result = [];
        foreach ($rooms as $room) {
            try {
                $this->bookingService->checkSlotAvailability($room->id, $date, $startTime, $durationHours);
                $result[$room->id] = 'free';
            } catch (\Throwable $e) {
                $result[$room->id] = 'busy';
            }
        }

        return response()->json($result);
    }

    /** Возвращает занятые интервалы по комнате и дате с учётом cleaning_end_time. */
    public function getBusySlots(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'duration_hours' => 'nullable|numeric|min:0.5',
        ]);

        $room = \App\Models\Room::find($request->room_id);

        if ($room->is_walk_in) {
            return response()->json([
                'busy_slots' => [],
                'buffer_minutes' => (int) \App\Models\Setting::get('cleaning_buffer_minutes', 15),
            ]);
        }

        $buffer = $room->cleaning_buffer_minutes
            ?? (int) \App\Models\Setting::get('cleaning_buffer_minutes', 15);

        $bookings = \App\Models\Booking::where('room_id', $request->room_id)
            ->whereDate('booking_date', $request->date)
            ->whereNotIn('status', ['cancelled'])
            ->get(['start_time', 'end_time', 'cleaning_end_time'])
            ->map(fn ($b) => [
                'start' => substr($b->start_time, 0, 5),
                'end' => substr($b->end_time, 0, 5),
                'cleaning_end' => substr($b->cleaning_end_time, 0, 5),
            ]);

        return response()->json([
            'busy_slots' => $bookings,
            'buffer_minutes' => $buffer,
        ]);
    }

    /** Сохранить заявку с сайта. */
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['source'] = 'online';
        $data['status'] = 'new';

        // Конвертация extra_service_ids в extra_services для DTO
        $extraServiceIds = $data['extra_service_ids'] ?? [];
        unset($data['extra_service_ids']);
        $data['extra_services'] = [];
        foreach ($extraServiceIds as $id) {
            $service = ExtraService::find($id);
            if ($service) {
                $data['extra_services'][] = [
                    'extra_service_id' => $id,
                    'quantity' => 1,
                    'price_at_booking' => (float) $service->price,
                ];
            }
        }

        $dto = BookingDTO::fromArray($data);
        $this->bookingService->create($dto, null);

        return back()->with('success', true);
    }
}
