<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellsRepresentative extends Model
{
    public static function forDropdown()
    {
        $query = SellsRepresentative::get();

        $sells_representative = $query->pluck('name','id');


        // if ($receipt_printer_type_attribute) {
        //     $attributes = collect($query->get())->mapWithKeys(function ($item) {
        //             return [$item->id => ['data-receipt_printer_type' => $item->receipt_printer_type]];
        //     })->all();

            return ['sells_representative' => $sells_representative];
        } 
}
