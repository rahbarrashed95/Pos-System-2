<?php

namespace App\Http\Controllers;

use App\Product;
use App\Variation;
use App\ProductDiscount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductDiscountController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {
            $discounts = ProductDiscount::leftJoin('products', 'products.id', '=', 'product_discounts.product_id')
            ->leftJoin('variations', 'variations.product_id', '=', 'products.id')
            ->select([
                'product_discounts.id AS id', 
                'products.name',
                'variations.default_sell_price',
                'product_discounts.discount_amount',
                'product_discounts.type',
                'product_discounts.final_product_price',
            ]);
            return Datatables::of($discounts)
            ->addColumn(
                'action',
                '<button data-href="{{ action(\'ProductDiscountController@edit\',[$id]) }}"  class="btn btn-xs btn-primary edit_product_discount "><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button> 
                <button data-href="{{action(\'ProductDiscountController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_product_discount"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
            )
            ->removeColumn('id')
            ->rawColumns([5])
            ->make(false);
        }
        return view('product_discount.index');
    }


    public function create()
    {
        
       return view('product_discount.add_product_discount');
    }


    public function store(Request $request)
    {
        if (request()->ajax()) {

            try {
                $product = Product::where('name',$request->product)->orWhere('sku', $request->product)->first();
                if($product){
                    $variation = Variation::select('variations.default_sell_price')
                    ->where('product_id', $product->id)->first();
                    $product_current_sell_price = $variation->default_sell_price;
                    $product_discount = new ProductDiscount;
                    $product_discount->type = $request->type;
                    $product_discount->discount_amount = $request->amount;
                    $product_discount->product_id = $product->id;
                    if($request->type=="fixed"){
                        $product_discount->final_product_price = $product_current_sell_price - $request->amount;
                    }else if($request->type=="percentage"){
                        $product_discount->final_product_price = $product_current_sell_price - (($request->amount/ 100 )* $product_current_sell_price);
                    }else{
                        return redirect()->back();
                    }
                    $product_discount->save();

                    return response()->json([
                        'success' => true,
                        'data' => $product_discount,
                        'msg' => __("product_discount.added_success")
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'msg' => __("Product Not Found")
                ]);
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ]);
            }
            
        }  
    }


    public function edit($id)
    {
        $discount = ProductDiscount::leftJoin('products', 'products.id', '=', 'product_discounts.product_id')
        ->leftJoin('variations', 'variations.product_id', '=', 'products.id')
        ->select('product_discounts.*','products.name', 'variations.default_sell_price')
        ->where('product_discounts.id', $id)->first();

        return view('product_discount.edit', compact('discount'));
        
    }


    public function update(Request $request)
    {
        if (request()->ajax()) {
            try {
                $product_discount = ProductDiscount::where('id', $request->id)->first();
                if($product_discount){
                    $product = Product::where('name',$request->product)->orWhere('sku', $request->product)->first();
                    if($product){
                        $variation = Variation::select('variations.default_sell_price')
                        ->where('product_id', $product->id)->first();
                        $product_current_sell_price = $variation->default_sell_price;

                        $product_discount->type = $request->type;
                        $product_discount->discount_amount = $request->amount;
                        $product_discount->product_id = $product->id;
                        if($request->type=="fixed"){
                            $product_discount->final_product_price = $product_current_sell_price - $request->amount;
                        }else if($request->type=="percentage"){
                            $product_discount->final_product_price = $product_current_sell_price - (($request->amount/ 100 )* $product_current_sell_price);
                        }
                        $product_discount->update();
                    }
                    return response()->json([
                        'success' => true,
                        'data' => $product_discount,
                        'msg' => __("product_discount.updated_success")
                    ]);

                }
            } catch (\Exception $e) {
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                        
                    return response()->json([
                        'success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ]);
            }
        }
    }

    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                $product_discount = ProductDiscount::find($id);
                $product_discount->delete();

                return response()->json([
                    'success' => true,
                    'data' => $product_discount,
                    'msg' => __("sr.deleted_success")
                ]);
        
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ]);
            }
            
        }
    }
}