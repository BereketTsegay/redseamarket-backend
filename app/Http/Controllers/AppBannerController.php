<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Common\Status;
use App\Models\AppBanner;
use App\Models\Country;

class AppBannerController extends Controller
{
    public function index(){

        $banner = AppBanner::orderBy('created_at', 'desc')
        ->get();
        $countries=Country::where('status',1)->get();
       
        return view('other.app_banner.index', compact('banner','countries'));
    }

    public function store(Request $request){

        $request->validate([
            'title'      => 'required',
            'image'     => 'required',
            'country'  => 'required',
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

        $banner             = new AppBanner();
        $banner->title       = $request->title;
        $banner->file      = $image;
        $banner->status     = $status;
        $banner->country_id = $request->country;
        $banner->save();

        session()->flash('success', 'Banner has been stored');
        return back();
    }

    public function view($id){

        $banner = AppBanner::where('id', $id)
        ->first();

        return view('other.app_banner.app_banner_detail', compact('banner'));
    }

    public function update(Request $request){
        
        $request->validate([
            'title'      => 'required',
            ]);

            $data=AppBanner::find($request->id);

        if($request->hasFile('image')){

            $file = uniqid().'.'.$request->image->getClientOriginalExtension();

            $request->image->storeAs('public/banner', $file);

            $image = 'storage/banner/'.$file;
            $data->file=$image;
        }
        else{
            $banner = AppBanner::where('id', $request->id)
            ->first();

            $image = $banner->image;
        }

        if($request->status == 'on'){
            $status = Status::ACTIVE;
        }
        else{
            $status = Status::INACTIVE;
        }

        $data->title=$request->title;
        $data->status=$status;

        $data->update();
       

        session()->flash('success', 'Banner has been updated');
        return back();
    }
}
