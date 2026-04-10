<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast-событие для админки: создана новая онлайн-бронь.
 */
class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function broadcastOn(): array
    {
        return [new Channel('admin-notifications')];
    }

    public function broadcastAs(): string
    {
        return 'booking.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->booking->id,
            'guest_name' => $this->booking->guest_name
                              ?? $this->booking->client?->first_name
                              ?? 'Гость',
            'room_name' => $this->booking->room
                ->getTranslation('name', 'ru'),
            'booking_date' => $this->booking->booking_date
                ->format('d.m.Y'),
            'start_time' => $this->booking->start_time,
            'amount' => $this->booking->final_amount,
            'source' => $this->booking->source,
        ];
    }
}
