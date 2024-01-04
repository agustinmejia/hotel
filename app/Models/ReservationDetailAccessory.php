<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationDetailAccessory extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'reservation_detail_id', 'room_accessory_id', 'price', 'observations', 'status'
    ];

    public function accessory() {
        return $this->belongsTo(RoomAccessory::class, 'room_accessory_id')->withTrashed();
    }
}
