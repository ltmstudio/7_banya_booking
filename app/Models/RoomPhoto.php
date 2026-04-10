<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPhoto extends Model
{
    use HasFactory;

    protected $table = 'room_photos';

    protected $fillable = ['room_id', 'path', 'sort_order'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
