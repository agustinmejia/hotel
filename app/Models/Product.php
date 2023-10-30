<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];

    public function type() {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function stock(){
        return $this->hasMany(ProductBranchOffice::class, 'product_id');
    }
}
