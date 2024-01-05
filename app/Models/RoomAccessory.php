<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomAccessory extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];

    public function reservation_accessories(){
        return $this->hasMany(ReservationDetailAccessory::class);
    }
}
