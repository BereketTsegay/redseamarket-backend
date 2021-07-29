<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(){

        $banner = Banner::where('status', Status::ACTIVE)
        ->get();

        return view('other.banner.banner', compact('banner'));
    }

    public function store(Request $request){

        $request->validate([
            'name'      => 'required',
            'position'   => 'required',
            'image'     => 'required|mimes:png,jpg,jpeg',
        ]);

        if($request->hasFile('image')){
            $file = uniqid().'.'.$request->image->getClientOriginalExtension();

            $request->image->storeAs('public/banner', $file);

            $image = 'storage/banner/'.$file;
        }

        if($request->status == 'on'){
            $status = Status::ACTIVE;
        }
        else{
            $status = Status::INACTIVE;
        }

        $banner     = new Banner();
        $banner->name   = $request->name;
        $banner->type   = $request->position;
        $banner->image  = $image;
        $banner->status = $status;
        $banner->save();

        session()->flash('success', 'Banner has been stored');
        return redirect()->route('banner.index');
    }

    public function view($id){

        $banner = Banner::where('id', $id)
        ->first();

        return view('other.banner.banner_details', compact('banner'));
    }

    public function update(Request $request){
        
        $request->validate([
            'name'      => 'required',
            'position'  => 'required',
            'image'     => 'mimes:png,jpg,jpeg',
        ]);

        if($request->hasFile('image')){

            $file = uniqid().'.'.$request->image->getClientOriginalExtension();

            $request->image->storeAs('public/banner', $file);

            $image = 'storage/banner/'.$file;
        }
        else{
            $banner = Banner::where('id', $request->id)
            ->first();

            $image = $banner->image;
        }

        if($request->status == 'on'){
            $status = Status::ACTIVE;
        }
        else{
            $status = Status::INACTIVE;
        }

        Banner::where('id', $request->id)
        ->update([
            'name'      => $request->name,
            'type'      => $request->position,
            'image'     => $image,
            'status'    => $status,
        ]);

        session()->flash('success', 'Banner has been updated');
        return redirect()->route('banner.index');
    }

    public function delete($id){

        Banner::destroy($id);

        session()->flash('success', 'Banner has been deleted');
        return redirect()->route('banner.index');
    }
}
