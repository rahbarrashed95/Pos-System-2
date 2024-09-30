<?php

namespace App\Http\Controllers;

use App\SellsRepresentative;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SRController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $srs = SellsRepresentative::select([
            'name', 'contact', 'id'
            ]);

            return Datatables::of($srs)
            ->addColumn(
                'action',
                '<button data-href="{{ action(\'SRController@edit\',[$id]) }}"  class="btn btn-xs btn-primary edit_sr "><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button> 
                <button data-href="{{action(\'SRController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_sr"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
            )
            ->removeColumn('id')
            ->rawColumns(['name', 'contact', 'action'])
            ->make(true);
        }

        return view('SR.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('SR.create');
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
            $input = $request->only(['name', 'contact']);
            $sr =new SellsRepresentative;
            $sr->name = $input['name'];
            $sr->contact = $input['contact'];
            $sr->save();

        return response()->json([
            'success' => true,
            'data' => $sr,
            'msg' => __("sr.added_success")
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
        $sr = SellsRepresentative::find($id);
        return view('SR.edit', compact('sr'));
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
                $sr = SellsRepresentative::findOrFail($input['id']);
                $sr->name = $input['name'];
                $sr->contact = $input['contact'];
                $sr->update();

                return response()->json([
                    'success' => true,
                    'data' => $sr,
                    'msg' => __("sr.updated_success")
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
                $sr = SellsRepresentative::find($id);
                $sr->delete();

                return response()->json([
                    'success' => true,
                    'data' => $sr,
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
