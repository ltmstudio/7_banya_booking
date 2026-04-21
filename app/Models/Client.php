<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'birthday',
        'password',
    ];

    protected $casts = [
        'birthday' => 'date',
        'password' => 'hashed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** Полное имя клиента. */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.($this->last_name ?? ''));
    }

    /** Scope именинников на сегодня. */
    public function scopeTodayBirthday($query)
    {
        return $query
            ->whereMonth('birthday', now()->month)
            ->whereDay('birthday', now()->day);
    }

    /** Связь клиента с бронями. */
    public function bookings()
    {
        return $this->hasMany(Booking::class)->latest('booking_date');
    }
}
