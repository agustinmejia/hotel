<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id', 'city_id', 'full_name', 'dni', 'phone', 'address', 'birthday', 'job', 'gender', 'photo'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id');
    }

    public function reservations() {
        return $this->hasMany(Reservation::class, 'person_id');
    }
}
