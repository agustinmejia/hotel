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
        return $this->belongsTo(Job::class, 'job_id');
    }
}
