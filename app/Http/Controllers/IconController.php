<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\FeaturedDealers;
use App\Models\IconClass;
use Illuminate\Http\Request;

class IconController extends Controller
{
    public function index(){

        $icon = IconClass::where('delete_status', '!=', Status::DELETE)
        ->orderBy('sort_order')
        ->get();

        return view('other.icons.icon', compact('icon'));
    }

    public function store(Request $request){
        
        $request->validate([
            'name'          => 'required',
            'sort_order'    => 'required|numeric',
        ]);

        if($request->status == 'checked'){
            
            $status = 1;
        }
        else{
            $status = 0;
        }

        $icon               = new IconClass();
        $icon->name         = $request->name;
        $icon->sort_order   = $request->sort_order;
        $icon->status       = $status;
        $icon->save();

        session()->flash('sucees', 'Icons has been stored');
        return redirect()->route('icon.index');
    }

    public function update(Request $request){

        $request->validate([
            'name'          => 'required',
            'sort_order'    => 'required|numeric',
        ]);

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        IconClass::where('id', $request->icon_id)
        ->update([
            'name'          => $request->name,
            'sort_order'    => $request->sort_order,
            'status'        => $status,
        ]);

        session()->flash('sucees', 'Icons has been updated');
        return redirect()->route('icon.index');
    }

    public function delete($id){

        IconClass::where('id', $id)
        ->update([
            'delete_status' => Status::DELETE,
        ]);

        session()->flash('sucees', 'Icons has been deleted');
        return redirect()->route('icon.index');
    }

    public function featuredIndex(){

        $featured = FeaturedDealers::orderBy('dealer_name')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('other.featured_dealer.dealer', compact('featured'));
    }

    public function featuredStore(Request $request){
        
        $request->validate([
            'name'      => 'required',
            'image'     => 'required|mimes:png,jpg,jpeg',
        ]);

        if($request->hasFile('image')){

            $image = uniqid().'.'. $request->image->getClientOriginalExtension();

            $request->image->storeAs('public/dealer', $image);

            $image = 'storage/dealer/'. $image;
        }

        if($request->status){
            $status = Status::ACTIVE;
        }
        else{
            $status = Status::INACTIVE;
        }

        $featured               = new FeaturedDealers();
        $featured->dealer_name  = $request->name;
        $featured->dealer_image = $image;
        $featured->status       = $status;
        $featured->save();

        session()->flash('sucees', 'Featured dealer has been stored');
        return redirect()->route('dealer.index');
    }

    public function featuredUpdate(Request $request){

        $request->validate([
            'name'      => 'required',
            'image'     => 'mimes:png,jpg,jpeg',
        ]);

        if($request->hasFile('image')){

            $image = uniqid().'.'. $request->image->getClientOriginalExtension();

            $request->image->storeAs('public/dealer', $image);

            $image = 'storage/dealer/'. $image;
        }
        else{
            $featured = FeaturedDealers::where('id', $request->id)
            ->first();
            $image = $featured->dealer_image;
        }

        if($request->status){
            $status = Status::ACTIVE;
        }
        else{
            $status = Status::INACTIVE;
        }

        FeaturedDealers::where('id', $request->id)
        ->update([
            'dealer_name'   => $request->name,
            'dealer_image'  => $image,
            'status'        => $status,
        ]);

        session()->flash('sucees', 'Featured dealer has been updated');
        return redirect()->route('dealer.index');
    }

    public function featuredDelete($id){

        FeaturedDealers::destroy($id);

        session()->flash('sucees', 'Featured dealer has been deleted');
        return redirect()->route('dealer.index');
    }
}
