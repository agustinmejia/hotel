<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeActivity extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'employe_id', 'user_id', 'room_id', 'description'
    ];

    public function employe(){
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    public function room(){
        return $this->belongsTo(Room::class, 'room_id')->withTrashed();
    }
}