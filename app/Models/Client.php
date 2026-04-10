<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'birthday',
    ];

    protected $casts = [
        'birthday' => 'date', // 👈 чтобы работало как Carbon объект
    ];

    // 👇 scope для именинников
    public function scopeTodayBirthday($query)
    {
        return $query
            ->whereMonth('birthday', now()->month)
            ->whereDay('birthday', now()->day);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
