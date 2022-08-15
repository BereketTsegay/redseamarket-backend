<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\ModelMst;
use App\Models\VarientMst;
use Illuminate\Http\Request;

class VarientMstController extends Controller
{
    
    public function index(){

        $varient_mst = VarientMst::orderBy('order')
        ->get();

        return view('ads.varient_mst.varient_mst', compact('varient_mst'));
    }

    public function create(){

        $models = ModelMst::where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();


        return view('ads.varient_mst.create_varient_mst', compact('models'));
    }

    public function store(Request $request){
     
        $request->validate([
            'varient_mst_name'     => 'required',
            'model_id'     => 'required',
            'order'        => 'required|numeric'
        ]);

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        $varient_mst                   = new VarientMst();
        $varient_mst->name             = $request->varient_mst_name;
        $varient_mst->model_id          = $request->model_id;
        $varient_mst->order       = $request->order;
        $varient_mst->status           = $status;
        $varient_mst->save();

        session()->flash('success', 'VarientMst has been created');
        return redirect()->route('varient_mst.index');

    }


    public function edit($id){

        $varient_mst = VarientMst::where('id', $id)
        ->first();
        
        $models = ModelMst::where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();

        return view('ads.varient_mst.edit_varient_mst', compact('varient_mst','models'));
    }

    public function update(Request $request, $id){

        $request->validate([
            'varient_mst_name'     => 'required',
            'order'        => 'required|numeric',
            'model_id'     => 'required'
        ]);


        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        VarientMst::where('id', $id)
        ->update([
            'name'              => $request->varient_mst_name,
            'model_id'             => $request->model_id,
            'order'        => $request->order,
            'status'            => $status
        ]);

        session()->flash('success', 'VarientMst has been updated');
        return redirect()->route('varient_mst.index');
    }

    public function delete($id){

        VarientMst::where('id', $id)->delete();

        session()->flash('success', 'VarientMst has been deleted');
        return redirect()->route('varient_mst.index');
    }



}
