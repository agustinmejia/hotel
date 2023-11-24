<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationDetailPenalty extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'reservation_detail_id', 'penalty_type_id', 'user_id', 'amount', 'observations', 'status'
    ];

    public function reservation_detail() {
        return $this->belongsTo(ReservationDetail::class, 'reservation_detail_id');
    }

    public function type() {
        return $this->belongsTo(PenaltyType::class, 'penalty_type_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
