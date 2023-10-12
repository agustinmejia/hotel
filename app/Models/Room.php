<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];

    public function type() {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }
}
