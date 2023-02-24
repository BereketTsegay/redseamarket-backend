<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\MakeMst;
use App\Models\ModelMst;
use Illuminate\Http\Request;

class ModelMstController extends Controller
{
    
    public function index(){

        $model_mst = ModelMst::orderBy('sort_order')
        ->get();

        return view('ads.model_mst.model_mst', compact('model_mst'));
    }

    public function create(){

        $makes = MakeMst::where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();


        return view('ads.model_mst.create_model_mst', compact('makes'));
    }

    public function store(Request $request){
     
        $request->validate([
            'model_mst_name'     => 'required',
            'make_id'     => 'required',
            'sort_order'        => 'required|numeric'
        ]);

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        $model_mst                   = new ModelMst();
        $model_mst->name             = $request->model_mst_name;
        $model_mst->make_id          = $request->make_id;
        $model_mst->sort_order       = $request->sort_order;
        $model_mst->status           = $status;
        $model_mst->save();

        session()->flash('success', 'Model has been created');
        return redirect()->route('model_mst.index');

    }


    public function edit($id){

        $model_mst = ModelMst::where('id', $id)
        ->first();
        
        $makes = MakeMst::where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();

        return view('ads.model_mst.edit_model_mst', compact('model_mst','makes'));
    }

    public function update(Request $request, $id){

        $request->validate([
            'model_mst_name'     => 'required',
            'sort_order'        => 'required|numeric',
            'make_id'     => 'required'
        ]);


        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        ModelMst::where('id', $id)
        ->update([
            'name'              => $request->model_mst_name,
            'make_id'             => $request->make_id,
            'sort_order'        => $request->sort_order,
            'status'            => $status
        ]);

        session()->flash('success', 'Model as been updated');
        return redirect()->route('model_mst.index');
    }

    public function delete($id){

        ModelMst::where('id', $id)->delete();

        session()->flash('success', 'Model has been deleted');
        return redirect()->route('model_mst.index');
    }



}
