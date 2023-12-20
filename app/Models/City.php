<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'state_id', 'name', 'province'
    ];
    public $additional_attributes = ['full_name'];

    public function state(){
        return $this->belongsTo(State::class, 'state_id');
    }

    public function getFullNameAttribute(){
        $state = State::find($this->state_id);
        return "{$this->name} - ".($state ? $state->name : '');
    }
}
