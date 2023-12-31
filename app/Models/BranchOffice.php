<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchOffice extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];

    public function products() {
        return $this->hasMany(ProductBranchOffice::class, 'branch_office_id');
    }
}
