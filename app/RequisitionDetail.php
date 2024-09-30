<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequisitionDetail extends Model
{
    protected $guarded = ['id'];

    public function requisition()
    {
        return $this->belongsTo(\App\Requisition::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }

    public function variations()
    {
        return $this->belongsTo(\App\Variation::class, 'variation_id');
    }
}
