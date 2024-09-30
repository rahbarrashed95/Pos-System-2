<?php

if (! function_exists('humanFilesize')) {
    function humanFilesize($size, $precision = 2)
    {
        $units = ['B','kB','MB','GB','TB','PB','EB','ZB','YB'];
        $step = 1024;
        $i = 0;

        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }
        
        return round($size, $precision).$units[$i];
    }
}

function get_stock($product_id,$variation_id){

    $total=0;
    $data=\App\VariationLocationDetails::where(['product_id'=>$product_id,'variation_id'=>$variation_id])->sum('qty_available');

    return $total +=$data;


}
