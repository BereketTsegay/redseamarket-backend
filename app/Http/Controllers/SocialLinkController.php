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
        ->orderBy('created_at', 'desc')
        ->get();

        $icon = IconClass::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->get();

        return view('other.social.social', compact('social', 'icon'));
    }

    public function store(Request $request){

        $request->validate([
            'name'      => 'required',
            'url'      => 'required',
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

    public function edit($id){

        $social = SocialLink::where('id', $id)
        ->first();

        $icon = IconClass::where('status', Status::ACTIVE)
        ->where('delete_status', '!=', Status::DELETE)
        ->get();

        return view('other.social.edit_social', compact('social', 'icon'));
    }

    public function update(Request $request, $id){
        
        $request->validate([
            'name'      => 'required',
            'url'      => 'required',
            'image'     => 'mimes:png,jpg,jpeg',
        ]);

        if($request->hasFile('image') || $request->icon){

            if($request->hasFile('image')){

                $file = uniqid().'.'.$request->image->getClientOriginalExtension();

                $request->image->storeAs('public/social/', $file);

                $image = 'storage/social'.$file;
            }
            else{
                $social = SocialLink::where('id', $id)
                ->first();

                $image = $social->image;
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

        SocialLink::where('id', $id)
        ->update([
            'name'      => $request->name,
            'url'       => $request->url,
            'icon'      => $request->icon,
            'image'     => $image,
            'status'    => $status,
        ]);

        session()->flash('success', 'Social Link has been updated');
        return redirect()->route('social.index');
    }

    public function delete($id){

        SocialLink::destroy($id);

        session()->flash('success', 'Social Link has been deleted');
        return redirect()->route('social.index');
    }
}
