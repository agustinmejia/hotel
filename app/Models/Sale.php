<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id', 'reservation_detail_id', 'person_id', 'date', 'observations', 'status'
    ];

    public function details() {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }
}
