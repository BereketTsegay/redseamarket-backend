<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

class TestimonialController extends Controller
{
    public function index(){

        $testimonial = Testimonial::get();

        return view('other.testimonial.testimonial', compact('testimonial'));
    }

    public function store(Request $request){

        $request->validate([
            'name'          => 'required',
            'designation'   => 'required',
            'description'   => 'required',
            'image'         => 'required|mimes:png,jpg,jpeg',
        ]);

        if($request->hasFile('image')){

            $file = uniqid().'.'.$request->image->getClientOriginalExtension();

            $request->image->storeAs('public/testimonial', $file);

            $image = 'storage/testimonial/'.$file;
        }

        $testimonial                = new Testimonial();
        $testimonial->name          = $request->name;
        $testimonial->designation   = $request->designation;
        $testimonial->description   = $request->description;
        $testimonial->image         = $image;
        $testimonial->save();

        session()->flash('success', 'Testimonial has been stored');
        return redirect()->route('testimonial.index');
    }

    public function view($id){

        $testimonial = Testimonial::where('id', $id)
        ->first();

        return view('other.testimonial.testimonial_details', compact('testimonial'));
    }

    public function edit($id){

        $testimonial = Testimonial::where('id', $id)
        ->first();

        return view('other.testimonial.edit_testimonial', compact('testimonial'));
    }

    public function update(Request $request, $id){

        $request->validate([
            'name'          => 'required',
            'designation'   => 'required',
            'description'   => 'required',
            'image'         => 'mimes:png,jpg,jpeg',
        ]);

        if($request->hasFile('image')){

            $file = uniqid().'.'.$request->image->getClientOriginalExtension();

            $request->image->storeAs('public/testimonial', $file);

            $image = 'storage/testimonial/'.$file;

        }
        else{
            $testimonial = Testimonial::where('id', $id)
            ->first();

            $image = $testimonial->image;
        }

        Testimonial::where('id', $id)
        ->update([
            'name' => $request->name,
            'designation'   => $request->designation,
            'description'   => $request->description,
            'image'         => $image,
        ]);

        session()->flash('success', 'Testimonial has been updated');
        return redirect()->route('testimonial.index');
    }
}
