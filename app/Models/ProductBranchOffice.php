<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBranchOffice extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function branch_office() {
        return $this->belongsTo(BranchOffice::class, 'branch_office_id');
    }
}
