<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationDetailFoodType extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'food_type_id', 'reservation_detail_id'
    ];

    public function type() {
        return $this->belongsTo(FoodType::class, 'food_type_id')->withTrashed();
    }
}
