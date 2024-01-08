<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employe extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];

    public function job(){
        return $this->belongsTo(Job::class, 'job_id')->withTrashed();
    }

    public function activities(){
        return $this->hasMany(EmployeActivity::class);
    }

    public function payments(){
        return $this->hasMany(EmployePayment::class);
    }
}
