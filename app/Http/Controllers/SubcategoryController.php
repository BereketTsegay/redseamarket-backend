<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function index(){

        $subcategory = Subcategory::orderBy('sort_order')
        ->where('status', Status::ACTIVE)
        ->get();

        $category = Category::orderBy('sort_order')
        ->where('status', Status::ACTIVE)
        ->get();

        return view('ads.subcategory.subcategory', compact('subcategory', 'category'));
    }

    public function create(){

        $category = Category::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();

        return view('ads.subcategory.create_subcategory', compact('category'));
    }

    public function store(Request $request){

        $request->validate([
            'category'          => 'required',
            'canonical_name'    => 'required',
            'sort_order'        => 'required|numeric',
            'subcategory_name'  => 'required',
            'description'       => 'required',
            'image'             => 'required|mimes:jpg,png,jpeg',
            'type'              => 'required',
            'value'             => 'required|numeric',
        ]);

        $category = explode('_', $request->category);

        if($category[0] == 'category'){

            $category_id = $category[1];
            $parent_id = 0;
        }
        else{

            $subcat = Subcategory::where('parent_id', $category[2])
            ->count();
            
            if($subcat > 1){
                $category_id = 0;
                $parent_id = $category[2];
            }
            else{
                $category_id = $category[1];
                $parent_id = $category[2];
            }
        }

        if($request->hasFile('image')){

            $image = uniqid().'.'.$request->image->getClientOriginalExtension();
            
            $request->image->storeAs('public/subcategory', $image);

            $image = 'storage/subcategory/'.$image;
        }

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        $subcategory                    = new Subcategory();
        $subcategory->category_id       = $category_id;
        $subcategory->parent_id         = $parent_id;
        $subcategory->name              = $request->subcategory_name;
        $subcategory->canonical_name    = $request->canonical_name;
        $subcategory->image             = $image;
        $subcategory->description       = $request->description;
        $subcategory->type              = $request->type;
        $subcategory->percentage        = $request->value;
        $subcategory->status            = $status;
        $subcategory->sort_order        = $request->sort_order;
        $subcategory->save();

        session()->flash('success', 'Subcategory has been created');
        return redirect()->route('subcategory.index');
    }

    public function view($id){

        $subcategory = Subcategory::where('id', $id)
        ->first();

        return view('ads.subcategory.subcategory_details', compact('subcategory'));
    }

    public function edit($id){

        $category = Category::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();

        $subcategory = Subcategory::where('id', $id)
        ->first();

        return view('ads.subcategory.edit_subcategory', compact('category', 'subcategory'));
    }

    public function update(Request $request, $id){
        
        $request->validate([
            'category'          => 'required',
            'canonical_name'    => 'required',
            'sort_order'        => 'required|numeric',
            'subcategory_name'  => 'required',
            'description'       => 'required',
            'image'             => 'mimes:jpg,png,jpeg',
            'type'              => 'required',
            'value'             => 'required|numeric',
        ]);

        $category = explode('_', $request->category);

        if($category[0] == 'category'){

            $category_id = $category[1];
            $parent_id = 0;
        }
        else{

            $subcat = Subcategory::where('parent_id', $category[2])
            ->count();
            
            if($subcat > 1){
                $category_id = 0;
                $parent_id = $category[2];
            }
            else{
                $category_id = $category[1];
                $parent_id = $category[2];
            }
        }

        if($request->hasFile('image')){

            $image = uniqid().'.'.$request->image->getClientOriginalExtension();
            
            $request->image->storeAs('public/subcategory', $image);

            $image = 'storage/subcategory/'.$image;
        }
        else{
            $subImage = Subcategory::where('id', $id)
            ->first();

            $image = $subImage->image;
        }

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        Subcategory::where('id', $id)
        ->update([
            'category_id'   => $category_id,
            'parent_id'     => $parent_id,
            'name'          => $request->subcategory_name,
            'image'         => $image,
            'description'   => $request->description,
            'type'          => $request->type,
            'percentage'    => $request->value,
            'status'        => $status,
            'sort_order'    => $request->sort_order,
        ]);

        session()->flash('success', 'Subcategory has been updated');
        return redirect()->route('subcategory.index');
    }

    public function delete($id){
        
        Subcategory::where('id', $id)
        ->update([
            'delete_status' => Status::DELETE,
        ]);

        session()->flash('success', 'Subcategory has been deleted');
        return redirect()->route('subcategory.index');
    }

    public function subcategoryAjaxfetch(Request $request){
        
            $subcategory = Subcategory::where('category_id', $request->id)
            ->where('delete_status', '!=', Status::DELETE)
            ->where('parent_id', 0)
            ->where('status', Status::ACTIVE)
            ->with('SubcategoryChild')
            ->orderBy('sort_order')
            ->get();

        return response()->json($subcategory);
    }

    public function subcategoryChange(Request $request){
        
        if($request->type == 'category'){
            $subcategory = Subcategory::where('category_id', $request->id)
            ->where('parent_id', 0)
            ->where('delete_status', '!=', Status::DELETE)
            ->where('status', Status::ACTIVE)
            ->orderBy('sort_order')
            ->get();
        }
        else{
            $subcategory = Subcategory::where('parent_id', $request->id)
            ->where('delete_status', '!=', Status::DELETE)
            ->where('status', Status::ACTIVE)
            ->orderBy('sort_order')
            ->get();
        }

        return response()->json($subcategory);
    }
}
