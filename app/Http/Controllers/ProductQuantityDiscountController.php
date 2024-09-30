<?php

namespace App\Http\Controllers;

use App\Product;
use App\QuantityDiscount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductQuantityDiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $quantity_discounts = QuantityDiscount::leftJoin('products as p','quantity_discounts.product_id', '=', 'p.id')
            ->select([
              'quantity_discounts.id', 'p.name as name',  'quantity_discounts.product_quantity', 'quantity_discounts.discount_product_quantity'
            ]);


            return Datatables::of($quantity_discounts)
            ->addColumn(
                'action',
                '<button data-href="{{ action(\'ProductQuantityDiscountController@edit\',[$id]) }}"  class="btn btn-xs btn-primary edit_quantity_discount "><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button> 
                <button data-href="{{action(\'ProductQuantityDiscountController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_quantity_discount"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
            )
            ->removeColumn('id')
            ->rawColumns([3])
            ->make(false);
        }
        
        return view('quantity_discount.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('quantity_discount.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {      
        try {
            $product = Product::where('name',$request->product)->orWhere('sku', $request->product)->first();
            $discount_product = Product::where('name',$request->discount_product)->orWhere('sku', $request->discount_product)->first();
            $input = $request->only(['sell_quantity', 'discount_quantity']);
            $quantity_discount =new QuantityDiscount;
            $quantity_discount->product_id =$product->id; 
            $quantity_discount->product_quantity = $input['sell_quantity'];
            // $quantity_discount->discount_product_id =$discount_product->id; 
            $quantity_discount->discount_product_quantity = $input['discount_quantity'];
            $quantity_discount->save();

        return response()->json([
            'success' => true,
            'data' => $quantity_discount,
            'msg' => __("Quantity Discount Added")
        ]);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $quantity_discount = QuantityDiscount::find($id)->leftJoin('products as p','quantity_discounts.product_id', '=', 'p.id')
        ->select( 'quantity_discounts.id', 'p.name',  'quantity_discounts.product_quantity', 'quantity_discounts.discount_product_quantity')->first();
        return view('quantity_discount.edit', compact('quantity_discount'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (request()->ajax()) {
                
        try {
            $product = Product::where('name',$request->product)->orWhere('sku', $request->product)->first();
            $discount_product = Product::where('name',$request->discount_product)->orWhere('sku', $request->discount_product)->first();
            $input = $request->only(['sell_quantity', 'discount_quantity']);
            $quantity_discount = QuantityDiscount::find($request->id);
            $quantity_discount->product_id =$product->id; 
            $quantity_discount->product_quantity = $input['sell_quantity'];
            // $quantity_discount->discount_product_id =$discount_product->id; 
            $quantity_discount->discount_product_quantity = $input['discount_quantity'];
            $quantity_discount->save();

                return response()->json([
                    'success' => true,
                    'data' => $quantity_discount,
                    'msg' => __("Quantity Discount Updated")
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                $quantity_discount = QuantityDiscount::find($id);
                $quantity_discount->delete();

                return response()->json([
                    'success' => true,
                    'data' => $quantity_discount,
                    'msg' => __("Quantity Discount Deleted")
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
