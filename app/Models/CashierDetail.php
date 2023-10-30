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
        'cashier_id', 'sale_detail_id', 'service_id', 'reservation_detail_day_id', 'type', 'amount', 'cash', 'observations'
    ];

    public function cashier(){
        return $this->belongsTo(Cashier::class, 'cashier_id');
    }
}
