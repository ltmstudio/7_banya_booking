<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Jobs\OptimizeRoomPhoto;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/** CRUD комнат в админке. */
class RoomController extends Controller
{
    public function index(Request $request): Response
    {
        $rooms = Room::with('photos')
            ->withCount(['bookings as active_bookings_count' => fn ($q) => $q->whereNotIn('status', ['cancelled', 'completed'])])
            ->orderBy('id')
            ->paginate((int) $request->get('per_page', 10));
        $roomsData = $rooms->getCollection()->map(function ($r) {
            $arr = (new RoomResource($r->load('photos')))->toArray(request());
            $arr['active_bookings_count'] = $r->active_bookings_count ?? 0;

            return $arr;
        })->values()->all();

        return Inertia::render('Panel/Rooms/Index', [
            'rooms' => $roomsData,
            'meta' => ['current_page' => $rooms->currentPage(), 'last_page' => $rooms->lastPage(), 'total' => $rooms->total()],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Panel/Rooms/Create');
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $photos = $data['photos'] ?? [];
        unset($data['photos']);

        $data['name'] = $data['name'] ?? ['ru' => '', 'tk' => ''];
        $data['description'] = $data['description'] ?? ['ru' => '', 'tk' => ''];

        $room = Room::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'category' => $data['category'],
            'price_per_hour' => $data['price_per_hour'],
            'promo_price_per_hour' => $data['promo_price_per_hour'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_walk_in' => (bool) ($data['is_walk_in'] ?? false),
            'capacity' => isset($data['capacity']) && $data['capacity'] !== '' ? (int) $data['capacity'] : null,
        ]);

        $this->storePhotos($room, $photos);

        AuditLog::write(Room::class, $room->id, 'create');

        return redirect()->route('panel.rooms.index')->with('success', __('room.created'));
    }

    public function show(Room $room): RedirectResponse
    {
        return redirect()->route('panel.rooms.edit', $room);
    }

    public function edit(Room $room): Response
    {
        $room->load('photos');

        return Inertia::render('Panel/Rooms/Edit', [
            'editingRoom' => (new RoomResource($room))->toArray(request()),
        ]);
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $data = $request->validated();
        $photos = $data['photos'] ?? [];
        unset($data['photos']);

        $data['name'] = $data['name'] ?? ['ru' => '', 'tk' => ''];
        $data['description'] = $data['description'] ?? ['ru' => '', 'tk' => ''];

        $room->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'category' => $data['category'],
            'price_per_hour' => $data['price_per_hour'],
            'promo_price_per_hour' => $data['promo_price_per_hour'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_walk_in' => (bool) ($data['is_walk_in'] ?? false),
            'capacity' => isset($data['capacity']) && $data['capacity'] !== '' ? (int) $data['capacity'] : null,
        ]);

        if (! empty($photos)) {
            $this->storePhotos($room, $photos);
        }

        AuditLog::write(Room::class, $room->id, 'update', 'fields', null, '[updated]');

        return redirect()->route('panel.rooms.index')->with('success', __('room.updated'));
    }

    public function destroy(Room $room): RedirectResponse
    {
        $activeCount = Booking::where('room_id', $room->id)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->count();

        if ($activeCount > 0) {
            return redirect()->back()->with('error', __('room.active_bookings_error'));
        }

        foreach ($room->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        $room->photos()->delete();
        $room->delete();

        AuditLog::write(Room::class, $room->id, 'delete');

        return redirect()->route('panel.rooms.index')->with('success', __('room.deleted'));
    }

    /** Переключение is_active. */
    public function toggleStatus(Request $request, Room $room): RedirectResponse
    {
        $newActive = ! $room->is_active;

        if (! $newActive) {
            $activeCount = Booking::where('room_id', $room->id)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->count();
            $cancelBookings = (bool) $request->input('cancel_bookings', false);

            if ($activeCount > 0 && ! $cancelBookings) {
                return redirect()->back()->with('error', __('room.active_bookings_error'));
            }
            if ($activeCount > 0 && $cancelBookings) {
                Booking::where('room_id', $room->id)
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->update(['status' => 'cancelled', 'cancel_reason' => 'Комната отключена']);
            }
        }

        $old = $room->is_active;
        $room->update(['is_active' => $newActive]);
        AuditLog::write(Room::class, $room->id, 'update', 'is_active', $old, $newActive);

        return redirect()->back()->with('success', __('room.updated'));
    }

    /** Удаление фото комнаты. */
    public function destroyPhoto(Room $room, RoomPhoto $photo): RedirectResponse
    {
        if ($photo->room_id !== $room->id) {
            abort(404);
        }
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return redirect()->back();
    }

    /** Сохранить загруженные фото в storage и создать RoomPhoto. */
    private function storePhotos(Room $room, array $files): void
    {
        $dir = 'rooms/'.$room->id;
        $sortOrder = $room->photos()->max('sort_order') ?? 0;

        foreach ($files as $file) {
            $path = $file->store($dir, 'public');
            $photo = $room->photos()->create(['path' => $path, 'sort_order' => ++$sortOrder]);
            OptimizeRoomPhoto::dispatch($photo);
        }
    }
}
