<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Room extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $table = 'rooms';

    protected $fillable = ['name', 'description', 'category', 'price_per_hour', 'child_price_per_hour', 'cleaning_buffer_minutes', 'promo_price_per_hour', 'is_active', 'is_walk_in', 'capacity'];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_walk_in' => 'boolean',
    ];

    /** Комната только для посещения без онлайн-бронирования. */
    public function isWalkIn(): bool
    {
        return (bool) $this->is_walk_in;
    }

    public function photos()
    {
        return $this->hasMany(RoomPhoto::class)->orderBy('sort_order');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
