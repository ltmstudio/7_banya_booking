<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingExtraService extends Model
{
    protected $table = 'booking_extra_service';

    protected $fillable = [
        'booking_id',
        'extra_service_id',
        'quantity',
        'price_at_booking',
    ];

    protected $casts = [
        'price_at_booking' => 'decimal:2',
        'quantity' => 'integer',
    ];
}
