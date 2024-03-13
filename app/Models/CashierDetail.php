<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashierDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'cashier_id', 'sale_detail_id', 'service_id', 'reservation_detail_day_id', 'reservation_detail_penalty_id', 'resort_register_id', 'type', 'amount', 'cash', 'observations'
    ];

    public function cashier(){
        return $this->belongsTo(Cashier::class, 'cashier_id');
    }

    public function sale_detail(){
        return $this->belongsTo(SaleDetail::class, 'sale_detail_id');
    }

    public function service(){
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function reservation_detail_day(){
        return $this->belongsTo(ReservationDetailDay::class, 'reservation_detail_day_id');
    }

    public function penalty(){
        return $this->belongsTo(ReservationDetailPenalty::class, 'reservation_detail_penalty_id');
    }

    public function resort_register(){
        return $this->belongsTo(ResortRegister::class, 'resort_register_id');
    }
}
