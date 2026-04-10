<?php

namespace App\Http\Controllers\Admin;

use App\DTO\BookingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Http\Requests\Booking\UpdateBookingStatusRequest;
use App\Http\Resources\BookingResource;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\ExtraService;
use App\Models\Room;
use App\Models\Setting;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** CRUD бронирований в админке. */
class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    /** Список с фильтрами: статус, комната, даты, поиск по клиенту. */
    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'room_id', 'date_from', 'date_to', 'search']);
        /** @var \Illuminate\Pagination\LengthAwarePaginator $bookings */
        $bookings = $this->bookingService->paginate(
            (int) $request->get('per_page', 15),
            $filters
        );

        $bookingsData = $bookings->getCollection()
            ->map(fn ($b) => (new BookingResource($b))->toArray(request()))
            ->values()
            ->all();

        $rooms = Room::orderBy('id')->get(['id', 'name', 'category'])->map(function ($r) {
            $name = $r->getTranslation('name', 'ru');

            return ['id' => $r->id, 'name' => $name, 'category' => $r->category];
        })->values()->all();

        $statuses = [
            ['value' => 'new', 'label' => __('booking.status_new')],
            ['value' => 'preliminary', 'label' => __('booking.status_preliminary')],
            ['value' => 'confirmed', 'label' => __('booking.status_confirmed')],
            ['value' => 'paid', 'label' => __('booking.status_paid')],
            ['value' => 'completed', 'label' => __('booking.status_completed')],
            ['value' => 'cancelled', 'label' => __('booking.status_cancelled')],
        ];

        $bookingsByDate = Booking::selectRaw('booking_date, COUNT(*) as count')
            ->whereYear('booking_date', now()->year)
            ->whereMonth('booking_date', now()->month)
            ->whereNotIn('status', ['cancelled'])
            ->groupBy('booking_date')
            ->pluck('count', 'booking_date')
            ->mapWithKeys(fn ($count, $date) => [Carbon::parse($date)->format('Y-m-d') => (int) $count])
            ->toArray();

        $stats = [
            'new_count' => Booking::where('status', 'new')->count(),
            'paid_count' => Booking::whereIn('status', ['paid', 'completed'])->count(),
            'total_revenue' => (int) Booking::whereIn('status', ['paid', 'completed'])->sum('final_amount'),
        ];

        return Inertia::render('Panel/Bookings/Index', [
            'bookings' => $bookingsData,
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
            ],
            'filters' => $filters,
            'rooms' => $rooms,
            'statuses' => $statuses,
            'bookingsByDate' => $bookingsByDate,
            'stats' => $stats,
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['status', 'room_id', 'date_from', 'date_to', 'search']);
        $bookings = $this->bookingService->getForExport($filters);

        $filename = 'bookings_'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'ID', 'Комната', 'Клиент', 'Телефон',
                'Дата', 'Начало', 'Конец', 'Часов',
                'Статус', 'Сумма базовая', 'Сумма итоговая',
                'Источник', 'Создано',
            ], ';');

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->id,
                    $b->room?->getTranslation('name', 'ru') ?? '',
                    $b->client?->full_name ?? $b->guest_name ?? '',
                    $b->client?->phone ?? $b->guest_phone ?? '',
                    $b->booking_date?->format('d.m.Y') ?? '',
                    $b->start_time ?? '',
                    $b->end_time ?? '',
                    $b->duration_hours ?? '',
                    $b->status ?? '',
                    $b->base_amount ?? '',
                    $b->final_amount ?? '',
                    $b->source ?? '',
                    $b->created_at?->format('d.m.Y H:i') ?? '',
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create(): Response
    {
        $rooms = Room::with('photos')->orderBy('id')->get();
        $roomsData = $rooms->map(fn ($r) => (new \App\Http\Resources\RoomResource($r))->toArray(request()))->values()->all();
        $clients = \App\Models\Client::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'phone'])->map(fn ($c) => [
            'id' => $c->id,
            'full_name' => trim($c->first_name.' '.($c->last_name ?? '')),
            'phone' => $c->phone,
        ])->values()->all();
        $extraServices = \App\Models\ExtraService::where('is_active', true)->orderBy('id')->get(['id', 'name', 'price']);

        return Inertia::render('Panel/Bookings/Create', [
            'rooms' => $roomsData,
            'clients' => $clients,
            'bookingSettings' => [
                'min_hours' => (int) Setting::get('booking_min_hours', 1),
                'max_hours' => (int) Setting::get('booking_max_hours', 10),
            ],
            'extraServices' => $extraServices->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->getTranslation('name', app()->getLocale()) ?? $s->name,
                'price' => (float) $s->price,
            ])->values()->all(),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $data = $this->prepareBookingData($request->validated());
        $dto = BookingDTO::fromArray($data);
        $userId = (int) auth()->id();
        $booking = $this->bookingService->create($dto, $userId);
        AuditLog::write('Booking', $booking->id, 'created');

        return redirect()->route('panel.bookings.show', $booking)->with('success', __('booking.created'));
    }

    public function show(Booking $booking): Response|RedirectResponse
    {
        $booking->load(['room', 'client', 'extraServices', 'createdBy']);
        $payload = ['booking' => (new BookingResource($booking))->toArray(request())];
        if (auth()->user()?->isAdmin()) {
            $payload['auditLogs'] = AuditLog::forEntity('Booking', $booking->id)
                ->with('user')
                ->latest()
                ->take(20)
                ->get()
                ->map(fn ($l) => [
                    'id' => $l->id,
                    'action' => $l->action,
                    'field' => $l->field,
                    'old_value' => $l->old_value,
                    'new_value' => $l->new_value,
                    'created_at' => $l->created_at?->format('d.m.Y H:i'),
                    'user_name' => $l->user?->name,
                ])
                ->values()
                ->all();
        }

        return Inertia::render('Panel/Bookings/Show', $payload);
    }

    public function edit(Booking $booking): Response
    {
        $booking->load(['room', 'client', 'extraServices']);
        $rooms = Room::with('photos')->orderBy('id')->get();
        $roomsData = $rooms->map(fn ($r) => (new \App\Http\Resources\RoomResource($r))->toArray(request()))->values()->all();
        $clients = \App\Models\Client::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'phone'])->map(fn ($c) => [
            'id' => $c->id,
            'full_name' => trim($c->first_name.' '.($c->last_name ?? '')),
            'phone' => $c->phone,
        ])->values()->all();
        $extraServices = \App\Models\ExtraService::where('is_active', true)->orderBy('id')->get(['id', 'name', 'price']);

        return Inertia::render('Panel/Bookings/Edit', [
            'booking' => (new BookingResource($booking))->toArray(request()),
            'rooms' => $roomsData,
            'clients' => $clients,
            'bookingSettings' => [
                'min_hours' => (int) Setting::get('booking_min_hours', 1),
                'max_hours' => (int) Setting::get('booking_max_hours', 10),
            ],
            'extraServices' => $extraServices->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->getTranslation('name', app()->getLocale()) ?? $s->name,
                'price' => (float) $s->price,
            ])->values()->all(),
        ]);
    }

    public function update(UpdateBookingRequest $request, Booking $booking): RedirectResponse
    {
        $data = $this->prepareBookingData($request->validated());
        $dto = BookingDTO::fromArray($data);
        $flat = $dto->toArray();
        foreach ($flat as $field => $newVal) {
            $oldVal = $booking->getAttribute($field);
            if ($oldVal != $newVal) {
                AuditLog::write('Booking', $booking->id, 'updated', $field, $oldVal, $newVal);
            }
        }
        $this->bookingService->update($booking, $dto);

        return redirect()->route('panel.bookings.show', $booking)->with('success', __('booking.updated'));
    }

    /** Смена статуса (и опционально итоговой суммы). */
    public function updateStatus(UpdateBookingStatusRequest $request, Booking $booking): RedirectResponse
    {
        if ($request->filled('final_amount')) {
            $this->bookingService->setFinalAmount($booking, (float) $request->input('final_amount'));
        }
        $this->bookingService->updateStatus(
            $booking,
            $request->validated('status'),
            $request->validated('cancel_reason')
        );

        return redirect()->back()->with('success', __('booking.status_updated'));
    }

    public function setFinalAmount(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate(['final_amount' => ['required', 'numeric', 'min:0']]);
        $this->bookingService->setFinalAmount($booking, (float) $request->input('final_amount'));

        return redirect()->back()->with('success', __('booking.final_amount_updated'));
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        /** @var \App\Models\User $user */
        if (! auth()->user()?->isAdmin()) {
            abort(403);
        }
        $booking->delete();
        AuditLog::write('Booking', $booking->id, 'deleted');

        return redirect()->route('panel.bookings.index')->with('success', __('booking.deleted'));
    }

    /** Преобразует extra_service_ids в extra_services для DTO. */
    private function prepareBookingData(array $data): array
    {
        if (empty($data['extra_service_ids'])) {
            unset($data['extra_service_ids']);

            return $data;
        }
        $services = ExtraService::whereIn('id', $data['extra_service_ids'])->get();
        $data['extra_services'] = [];
        foreach ($services as $s) {
            $data['extra_services'][] = [
                'extra_service_id' => $s->id,
                'quantity' => 1,
                'price_at_booking' => (float) $s->price,
            ];
        }
        unset($data['extra_service_ids']);

        return $data;
    }
}
