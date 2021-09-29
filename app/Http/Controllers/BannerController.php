<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\Banner;
use App\Models\Country;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(){

        $banner = Banner::where('status', Status::ACTIVE)
        ->orderBy('created_at', 'desc')
        ->get();

        $country = Country::orderBy('name')
        ->get();

        return view('other.banner.banner', compact('banner', 'country'));
    }

    public function store(Request $request){

        $request->validate([
            'name'      => 'required',
            'country'   => 'required',
            'image'     => 'required|mimes:png,jpg,jpeg|dimensions:width=1920,height=506',
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

        $banner             = new Banner();
        $banner->name       = $request->name;
        $banner->country_id = $request->country;
        $banner->image      = $image;
        $banner->status     = $status;
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
            'country'   => 'required',
            'image'     => 'mimes:png,jpg,jpeg|dimensions:width=1920,height=506',
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
            'name'          => $request->name,
            'country_id'    => $request->country,
            'image'         => $image,
            'status'        => $status,
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
