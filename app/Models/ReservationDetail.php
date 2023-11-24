<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'reservation_id', 'room_id', 'price', 'observations', 'status'
    ];

    public function reservation() {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function room() {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function accessories() {
        return $this->hasMany(ReservationDetailAccessory::class, 'reservation_detail_id');
    }

    public function penalties() {
        return $this->hasMany(ReservationDetailPenalty::class, 'reservation_detail_id');
    }

    public function days() {
        return $this->hasMany(ReservationDetailDay::class, 'reservation_detail_id');
    }

    public function sales() {
        return $this->hasMany(Sale::class, 'reservation_detail_id');
    }
}
