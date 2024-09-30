<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
     protected $guarded = ['id'];
    
    public function details()
    {
        return $this->hasMany(\App\RequisitionDetail::class);
    }

    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

     public function contact()
    {
        return $this->belongsTo(\App\Contact::class, 'contact_id');
    }
}
