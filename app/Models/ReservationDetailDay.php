<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationDetailDay extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'reservation_detail_id', 'date', 'amount', 'observations', 'status'
    ];

    /**
     * Get the user that owns the ReservationDetailDay
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reservation_detail(){
        return $this->belongsTo(ReservationDetail::class, 'reservation_detail_id');
    }
}
