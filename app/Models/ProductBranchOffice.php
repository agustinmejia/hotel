<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBranchOffice extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'branch_office_id', 'product_id', 'price', 'quantity', 'initial_quantity'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function branch_office() {
        return $this->belongsTo(BranchOffice::class, 'branch_office_id')->withTrashed();
    }

    public function change_stock(){
        return $this->hasMany(ProductBranchOfficeStockChange::class, 'product_branch_office_id');
    }
}
