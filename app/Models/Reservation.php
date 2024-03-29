<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id', 'person_id', 'start', 'finish', 'amount', 'discount', 'reason', 'observation', 'status'
    ];

    public function details() {
        return $this->hasMany(ReservationDetail::class, 'reservation_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function person() {
        return $this->belongsTo(Person::class, 'person_id')->withTrashed();
    }

    public function aditional_people(){
        return $this->hasMany(ReservationPerson::class, 'reservation_id');
    }
}
