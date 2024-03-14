<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResortRegister extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id', 'branch_office_id', 'type', 'quantity', 'price', 'observations'
    ];

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class, 'branch_office_id');
    }
}
