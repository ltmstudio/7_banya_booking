<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ExtraService;
use App\Models\PopupPromo;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/** Главная страница лендинга: комнаты, доп. услуги, промо-попап. */
class HomeController extends Controller
{
    public function index(): Response
    {
        \Log::info('HomeController index start');
        $rooms = Room::where('is_active', true)
            ->with('photos')
            ->orderBy('id')
            ->get();

        $extraServices = ExtraService::where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->getTranslations('name') ?: ['ru' => $s->name, 'tk' => $s->name],
                'icon_url' => $s->icon_path ? \Illuminate\Support\Facades\Storage::url($s->icon_path) : null,
                'price' => (float) $s->price,
            ])
            ->values()
            ->all();

        $popupPromo = PopupPromo::where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        $popupPromoData = null;
        if ($popupPromo) {
            $popupPromoData = [
                'id' => $popupPromo->id,
                'title' => $popupPromo->getTranslations('title') ?: ['ru' => '', 'tk' => ''],
                'body' => $popupPromo->getTranslations('body') ?: ['ru' => '', 'tk' => ''],
            ];
        }

        return Inertia::render('Landing/Welcome', [
            'canLogin' => \Illuminate\Support\Facades\Route::has('login'),
            'bookingSettings' => [
                'min_hours' => (int) Setting::get('booking_min_hours', 1),
                'max_hours' => (int) Setting::get('booking_max_hours', 10),
            ],
            'rooms' => $rooms->map(fn ($room) => [
                'id' => $room->id,
                'name' => $room->getTranslations('name') ?: ['ru' => (string) $room->name, 'tk' => (string) $room->name],
                'description' => $room->getTranslations('description') ?: ['ru' => '', 'tk' => ''],
                'category' => $room->category,
                'category_label' => match ($room->category) {
                    'family' => 'Семейная',
                    'vip' => 'VIP',
                    default => 'Обычная',
                },
                'price_per_hour' => (float) $room->price_per_hour,
                'promo_price_per_hour' => $room->promo_price_per_hour ? (float) $room->promo_price_per_hour : null,
                'child_price_per_hour' => $room->child_price_per_hour ? (float) $room->child_price_per_hour : null,
                'capacity' => $room->capacity,
                'is_active' => (bool) $room->is_active,
                'is_walk_in' => (bool) ($room->is_walk_in ?? false),
                'photos' => $room->photos->map(fn ($p) => [
                    'id' => $p->id,
                    'url' => Storage::url($p->path),
                ])->values()->all(),
            ])->values()->all(),
            'extraServices' => $extraServices,
            'popupPromo' => $popupPromoData,
            'landing' => [],
        ]);
    }
}
