<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cashier extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id', 'branch_office_id', 'observations', 'status', 'closed_at'
    ];

    public function details(){
        return $this->hasMany(CashierDetail::class, 'cashier_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class, 'branch_office_id');
    }
}
