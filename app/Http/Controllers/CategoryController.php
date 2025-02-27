<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\IconClass;
use App\Models\ModelMst;
use App\Models\State;
use App\Models\VarientMst;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function index(){

        $category = Category::orderBy('sort_order')
        ->where('delete_status', '!=', Status::DELETE)
        ->get();

        return view('ads.category.category', compact('category'));
    }

    public function create(){

        $icon = IconClass::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();

        $country = Country::orderBy('name')
        ->get();

        return view('ads.category.create_category', compact('icon', 'country'));
    }

    public function store(Request $request){
     
        $request->validate([
            'category_name'     => 'required',
            // 'icon_class'        => 'required|numeric',
            // 'city'              => 'required|numeric',
            // 'state'             => 'required|numeric',
            'canonical_name'    => 'required',
            'arabic_name'           => 'required',
            'sort_order'        => 'required|numeric',
            'expire_days'       => 'required|numeric',
            'description'       => 'required',
            'image'             => 'required|mimes:jpeg,jpg,png',
        ]);

        if($request->hasFile('image')){

            $image = uniqid().'.'.$request->image->getClientOriginalExtension();
            
            $request->image->storeAs('public/category', $image);

            $image = 'storage/category/'.$image;

        }

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        if($request->display_flag == 'on'){
            
            $count = Category::where('display_flag', Status::ACTIVE)->where('delete_status', '!=', Status::DELETE)
            ->count();
           // return $count;
            if($count <= 7){
                $display_flag = Status::ACTIVE;
            }
            else{
                session()->flash('status_error', 'Display Count is exceeded');
                return redirect()->back();
            }
        }
        else{
            $display_flag = 0;
        }

        $category                   = new Category();
        $category->name             = $request->category_name;
        $category->canonical_name   = $request->canonical_name;
        $category->description      = $request->description;
        $category->image            = $image;
         $category->arabic_name    = $request->arabic_name;
        // $category->country_id       = $request->country;
        $category->type         = $request->type;
        $category->percentage          = $request->value;
        $category->sort_order       = $request->sort_order;
        $category->expire_days        = $request->expire_days;
        $category->status           = $status;
        $category->display_flag     = $display_flag;
        $category->reserved_flag    = 0;
        $category->save();

        session()->flash('success', 'Category has been created');
        return redirect()->route('category.index');

    }

    public function view($id){

        $category = Category::where('id', $id)
        ->first();

        return view('ads.category.category_details', compact('category'));
    }

    public function edit($id){

        $category = Category::where('id', $id)
        ->first();

        $icon = IconClass::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();

        $country = Country::orderBy('name')
        ->get();

        return view('ads.category.edit_category', compact('category', 'icon', 'country'));
    }

    public function editExpire($id){
        $category = Category::find($id);
        return view('ads.category.category_expire', compact('category'));
    }
    public function update(Request $request, $id){

        $request->validate([
            'category_name'     => 'required',
            'arabic_name'        => 'required',
            // 'city'              => 'required|numeric',
            'canonical_name'    => 'required',
            // 'country'           => 'required|numeric',
            // 'state'             => 'required|numeric',
            // 'expire_days'       => 'required|numeric',
            'sort_order'        => 'required|numeric',
            'description'       => 'required',
            'image'             => 'mimes:jpeg,jpg,png',
        ]);

        if($request->hasFile('image')){

            $image = uniqid().'.'.$request->image->getClientOriginalExtension();
            
            $request->image->storeAs('public/category', $image);

            $image = 'storage/category/'.$image;

        }
        else{

            $category = Category::where('id', $id)
            ->first();

            $image = $category->image;
        }

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        if($request->display_flag == 'on'){

            $count = Category::where('display_flag', Status::ACTIVE)->where('delete_status', '!=', Status::DELETE)
            ->count();
            $category = Category::where('id', $id)
            ->first();
            if($count <= 7 || $category->display_flag==1){
                $display_flag = Status::ACTIVE;
            }
            else{
                session()->flash('status_error', 'Display Count is exceeded');
                return redirect()->back();
            }
        }
        else{
            $display_flag = 0;
        }

        if($id == 1 || $id == 2 || $id == 3){
            $reserved = 1;
        }
        else{
            $reserved = 0;
        }

        Category::where('id', $id)
        ->update([
            'name'              => $request->category_name,
            'canonical_name'    => $request->canonical_name,
            'description'       => $request->description,
            'image'             => $image,
            'arabic_name'     => $request->arabic_name,
            // 'country_id'        => $request->country,
            // 'state_id'          => $request->state,
            // 'city_id'           => $request->city,
            'type'         => $request->type,
            'percentage'          => $request->value,
            'sort_order'        => $request->sort_order,
            'expire_days'        => $request->expire_days,
            'status'            => $status,
            'display_flag'      => $display_flag,
            'reserved_flag'     => $reserved,
        ]);

        session()->flash('success', 'Category has been updated');
        return redirect()->route('category.index');
    }

    public function updateExpiry(Request $request,$id){
        $request->validate([
           
            'expire_days'       => 'required|numeric',
          
        ]);

        Category::where('id', $id)
        ->update([  
            'expire_days'        => $request->expire_days,    
        ]);

        session()->flash('success', 'Expiry Days has been updated');
        return redirect()->route('category.index');
    }

    public function delete($id){

        Category::where('id', $id)
        ->update([
            'delete_status' => Status::DELETE,
        ]);

        session()->flash('success', 'Category has been deleted');
        return redirect()->route('category.index');
    }

    public function getState(Request $request){

        $request->validate([
            'id'    => 'required|numeric',
        ]);

        $state = State::where('country_id', $request->id)
        ->orderBy('name')
        ->get();

        return response()->json($state);
    }

    public function getCity(Request $request){
        
        $request->validate([
            'id'    => 'required|numeric',
        ]);

        $city = City::where('state_id', $request->id)
        ->orderBy('name')
        ->get();

        return response()->json($city);
    }

    public function getVehicleModel(Request $request){

        $request->validate([
            'id'    => 'required|numeric',
        ]);

        $model = ModelMst::where('make_id', $request->id)
        ->where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();

        return response()->json($model);
    }

    public function getVehicleVarient(Request $request){

        $request->validate([
            'id'    => 'required|numeric',
        ]);

        $varient = VarientMst::where('model_id', $request->id)
        ->where('status', Status::ACTIVE)
        ->orderBy('order')
        ->get();

        return response()->json($varient);        
    }
}
