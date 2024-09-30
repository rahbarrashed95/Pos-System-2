<?php

namespace App\Http\Controllers;

use App\DeliveryMan;
use App\SellsRepresentative;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DeliveryManController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
                $delivery_mans= DeliveryMan::select([
                'name', 'contact', 'id'
                ]);
                
            return Datatables::of($delivery_mans)
            ->addColumn(
                'action',
                '<button data-href="{{ action(\'DeliveryManController@edit\',[$id]) }}"  class="btn btn-xs btn-primary edit_delivery_man"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button> 
                <button data-href="{{ action(\'DeliveryManController@destroy\',[$id]) }}" class="btn btn-xs btn-danger delete_delivery_man"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
            )
            ->removeColumn('id')
            ->rawColumns([2])
            ->make(false);
        }
        return view('delivery_man.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        return view('delivery_man.create');
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'contact']);
                $delivery_man = new DeliveryMan;
                $delivery_man->name = $input['name'];
                $delivery_man->contact = $input['contact'];
                $delivery_man->save();

                return response()->json([
                    'success' => true,
                    'data' => $delivery_man,
                    'msg' => __("delivery.added_success")
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
       $delivery_man = DeliveryMan::find($id);
        return view('delivery_man.edit', compact('delivery_man'));
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
                
                $input = $request->only(['name', 'contact', 'id']);
                $delivery_man = DeliveryMan::findOrFail($input['id']);
                $delivery_man->name = $input['name'];
                $delivery_man->contact = $input['contact'];
                $delivery_man->update();
                return response()->json([
                    'success' => true,
                    'data' => $delivery_man,
                    'msg' => __("delivery.updated_success")
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
                $delivery_man = DeliveryMan::find($id);
                $delivery_man->delete();

                return response()->json([
                    'success' => true,
                    'data' => $delivery_man,
                    'msg' => __("delivery.deleted_success")
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
