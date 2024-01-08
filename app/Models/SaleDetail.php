<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'sale_id', 'product_id', 'price', 'quantity', 'status'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
}
