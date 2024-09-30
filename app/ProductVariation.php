<?php

namespace App;

use App\Variation;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    public function variations()
    {
        return $this->hasMany(Variation::class, 'product_id', 'product_id');
    }

    public function variation_template()
    {
        return $this->belongsTo(\App\VariationTemplate::class);
    }
}
