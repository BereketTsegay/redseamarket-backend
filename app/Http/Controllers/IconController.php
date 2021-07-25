<?php

namespace App\Http\Controllers;

use App\Common\Status;
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
}
