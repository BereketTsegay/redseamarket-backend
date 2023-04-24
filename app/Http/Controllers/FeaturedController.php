<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Featured;
class FeaturedController extends Controller
{
    
    public function index(){
     $data=Featured::first();
    // return $data;
     return view('featured',compact('data'));
    }

    public function update(Request $request){
     // return $request;
        $data=Featured::find($request->id);
        $data->featured=$request->featured;
        $data->update();
        session()->flash('success', 'Featured has been updated');
        return redirect()->route('admin.featured');
        
    }
}
