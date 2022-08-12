<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\MakeMst;
use App\Models\IconClass;
use App\Models\ModelMst;
use Illuminate\Http\Request;

class MakeMstController extends Controller
{
    
    public function index(){

        $make_mst = MakeMst::orderBy('sort_order')
        ->get();

        return view('ads.make_mst.make_mst', compact('make_mst'));
    }

    public function create(){

        $icon = IconClass::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();


        return view('ads.make_mst.create_make_mst', compact('icon'));
    }

    public function store(Request $request){
     
        $request->validate([
            'make_mst_name'     => 'required',
            'sort_order'        => 'required|numeric',
            'image'             => 'required|mimes:jpeg,jpg,png',
        ]);

        if($request->hasFile('image')){

            $image = uniqid().'.'.$request->image->getClientOriginalExtension();
            
            $request->image->storeAs('public/make_mst', $image);

            $image = 'storage/make_mst/'.$image;

        }

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        $make_mst                   = new MakeMst();
        $make_mst->name             = $request->make_mst_name;
        $make_mst->image            = $image;
        $make_mst->sort_order       = $request->sort_order;
        $make_mst->status           = $status;
        $make_mst->save();

        session()->flash('success', 'MakeMst has been created');
        return redirect()->route('make_mst.index');

    }

    public function view($id){

        $make_mst = MakeMst::where('id', $id)
        ->first();

        return view('ads.make_mst.make_mst_details', compact('make_mst'));
    }

    public function edit($id){

        $make_mst = MakeMst::where('id', $id)
        ->first();

        $icon = IconClass::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();


        return view('ads.make_mst.edit_make_mst', compact('make_mst', 'icon'));
    }

    public function update(Request $request, $id){

        $request->validate([
            'make_mst_name'     => 'required',
            'sort_order'        => 'required|numeric',
            'image'             => 'mimes:jpeg,jpg,png',
        ]);

        if($request->hasFile('image')){

            $image = uniqid().'.'.$request->image->getClientOriginalExtension();
            
            $request->image->storeAs('public/make_mst', $image);

            $image = 'storage/make_mst/'.$image;

        }
        else{

            $make_mst = MakeMst::where('id', $id)
            ->first();

            $image = $make_mst->image;
        }

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        MakeMst::where('id', $id)
        ->update([
            'name'              => $request->make_mst_name,
            'image'             => $image,
            'sort_order'        => $request->sort_order,
            'status'            => $status
        ]);

        session()->flash('success', 'MakeMst has been updated');
        return redirect()->route('make_mst.index');
    }

    public function delete($id){

        MakeMst::where('id', $id)->delete();

        session()->flash('success', 'MakeMst has been deleted');
        return redirect()->route('make_mst.index');
    }

    public function getVehicleModel(Request $request){

        $request->validate([
            'id'    => 'required|numeric',
        ]);

        $model = ModelMst::where('make_id', $request->id)
        ->where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();

        return response()->json($model);
    }

}
