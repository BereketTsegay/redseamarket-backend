<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\IconClass;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function index(){

        $social = SocialLink::where('status', Status::ACTIVE)
        ->get();

        $icon = IconClass::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->get();

        return view('other.social.social', compact('social', 'icon'));
    }

    public function store(Request $request){

        $request->validate([
            'name'      => 'required',
            'icon'      => 'required',
            'image'     => 'mimes:png,jpg,jpeg',
        ]);

        if($request->hasFile('image') || $request->icon){

            if($request->hasFile('image')){

                $file = uniqid().'.'.$request->image->getClientOriginalExtension();

                $request->image->storeAs('public/social/', $file);

                $image = 'storage/social'.$file;
            }
            else{
                $image = null;
            }
        }
        else{
            $request->validate([
                'icon'      => 'required',
            ]);
        }

        if($request->status == 'on'){
            $status = Status::ACTIVE;
        }
        else{
            $status = Status::INACTIVE;
        }

        $social     = new SocialLink();
        $social->name   = $request->name;
        $social->url    = $request->url;
        $social->image  = $image;
        $social->icon   = $request->icon;
        $social->status = $status;
        $social->save();

        session()->flash('success', 'Social Link has been stored');
        return redirect()->route('social.index');
    }
}
