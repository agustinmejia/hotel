<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'room_type_id', 'branch_office_id', 'floor_number', 'code', 'details', 'images', 'status'
    ];

    public function type() {
        return $this->belongsTo(RoomType::class, 'room_type_id')->withTrashed();
    }

    public function reservation_detail() {
        return $this->hasMany(ReservationDetail::class, 'room_id');
    }
}
