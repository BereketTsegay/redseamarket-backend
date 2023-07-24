<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Common\Status;
use App\Models\AppBanner;

class AppBannerController extends Controller
{
    public function index(){

        $banner = AppBanner::orderBy('created_at', 'desc')
        ->get();

       
        return view('other.app_banner.index', compact('banner'));
    }

    public function store(Request $request){

        $request->validate([
            'title'      => 'required',
            'image'     => 'required',
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
        $banner->save();

        session()->flash('success', 'Banner has been stored');
        return back();
    }

    public function view($id){

        $banner = AppBanner::where('id', $id)
        ->first();

        return view('other.banner.banner_details', compact('banner'));
    }

    public function update(Request $request){
        
        $request->validate([
            'title'      => 'required',
            'image'   => 'required',
        ]);

        if($request->hasFile('image')){

            $file = uniqid().'.'.$request->image->getClientOriginalExtension();

            $request->image->storeAs('public/banner', $file);

            $image = 'storage/banner/'.$file;
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

        AppBanner::where('id', $request->id)
        ->update([
            'title'          => $request->title,
            'file'         => $image,
            'status'        => $status,
        ]);

        session()->flash('success', 'Banner has been updated');
        return back();
    }
}
