<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'client_id',
        'created_by',
        'booking_date',
        'start_time',
        'end_time',
        'cleaning_end_time',
        'end_at',
        'duration_hours',
        'guests_count',
        'status',
        'source',
        'cancel_reason',
        'base_amount',
        'final_amount',
        'is_promo_applied',
        'is_online_payment',
        'online_payment_status',
        'online_payment_ref',
        'guest_name',
        'guest_phone',
        'guest_last_name',
        'guest_birthday',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'guest_birthday' => 'date',
        'end_at' => 'datetime',
        'duration_hours' => 'float',
        'is_promo_applied' => 'boolean',
        'is_online_payment' => 'boolean',
        'base_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    // ─── Связи ───────────────────────────────────────

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function extraServices()
    {
        return $this->belongsToMany(ExtraService::class, 'booking_extra_service')
            ->using(BookingExtraService::class) // 👈 добавить
            ->withPivot('quantity', 'price_at_booking')
            ->withTimestamps();
    }

    // public function receipt()
    // {
    //     return $this->hasOne(Receipt::class);
    // }

    // ─── Scopes ──────────────────────────────────────

    // Брони на сегодня
    public function scopeToday($query)
    {
        return $query->whereDate('booking_date', today());
    }

    // Активные брони (не отменённые и не завершённые)
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'completed']);
    }

    // ─── Helpers ─────────────────────────────────────

    // Проверка — заявка пришла с сайта
    public function isOnline(): bool
    {
        return $this->source === 'online';
    }

    // Проверка — клиент ещё не привязан
    public function hasNoClient(): bool
    {
        return is_null($this->client_id);
    }
}
