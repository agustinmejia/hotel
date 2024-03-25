<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonDefaulter extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'cashier_id', 'person_id', 'reservation_detail_id', 'amount', 'type', 'observations', 'status'
    ];

    public function cashier(){
        return $this->belongsTo(Cashier::class)->withTrashed();
    }

    public function person(){
        return $this->belongsTo(Person::class)->withTrashed();
    }

    public function reservation_detail() {
        return $this->belongsTo(ReservationDetail::class);
    }

    public function payments(){
        return $this->hasMany(PersonDefaulterPayment::class, 'person_defaulter_id');
    }
}
