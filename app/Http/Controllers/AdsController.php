<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\Ads;
use App\Models\AdsCustomValue;
use App\Models\AdsImage;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\Country;
use App\Models\FieldOptions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdsController extends Controller
{
    public function index(){

        $ad = Ads::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->paginate(10);

        return view('ads.ad_list', compact('ad'));
    }

    public function create(){
        
        $category = Category::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->get();

        $country = Country::orderBy('name')
        ->get();

        return view('ads.create_ad', compact('category', 'country'));
    }

    public function store(Request $request){

        $request->validate([
            'category'          => 'required|numeric',
            'title'             => 'required',
            'price'             => 'required|numeric',
            'state'             => 'required|numeric',
            'sort_order'        => 'required',
            'subcategory'       => 'numeric',
            'canonical_name'    => 'required',
            'country'           => 'required|numeric',
            'city'              => 'required|numeric',
            'description'       => 'required',
            'image.*'           => 'required|mimes:png,jpg,jpeg',
        ]);
        
        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        if($request->negotiable == 'checked'){
            $negotiable_flag = 1;
        }
        else{
            $negotiable_flag = 0;
        }

        if($request->featured == 'checked'){
            $featured_flag = 1;
        }
        else{
            $featured_flag = 0;
        }
        
        $categoryField = CategoryField::where('category_id', $request->category)
        ->with(['Field' => function($a){
            $a->where('delete_status', '!=', Status::DELETE)
            ->with(['FieldOption' => function($q){
                $q->where('delete_status', '!=', Status::DELETE);
            }]);
        }])
        ->get();

        $ad                         = new Ads();
        $ad->category_id            = $request->category;
        $ad->subcategory_id         = $request->subcategory;
        $ad->title                  = $request->title;
        $ad->canonical_name         = $request->canonical_name;
        $ad->description            = $request->description;
        $ad->price                  = $request->price;
        $ad->negotiable_flag        = $negotiable_flag;
        $ad->country_id             = $request->country;
        $ad->state_id               = $request->state;
        $ad->city_id                = $request->city;
        $ad->sellerinformation_id   = 0; //Admin
        $ad->customer_id            = Auth()->user()->id;
        $ad->featured_flag          = $featured_flag;
        $ad->latitude               = $request->address_latitude;
        $ad->longitude              = $request->address_longitude;
        $ad->status                 = $status;
        $ad->save();

        if($request->hasFile('image')){

            foreach($request->image as $row){

                $image = uniqid().'.'.$row->getClientOriginalExtension();
            
                $row->storeAs('public/ads', $image);

                $image = 'storage/ads/'.$image;

                $adImage            = new AdsImage();
                $adImage->ads_id    = $ad->id;
                $adImage->image     = $image;
                $adImage->save();
            }
        }

        foreach($categoryField as $catRow){
            
            if($catRow->Field->type == 'select'){
                $option_id = $request->select;
                
                $fieldOption = FieldOptions::where('id', $option_id)
                ->where('field_id', $catRow->field_id)
                ->first();
                
                $optionValue = $fieldOption->value;
                
                $customValue            = new AdsCustomValue();
                $customValue->ads_id    = $ad->id;
                $customValue->field_id  = $catRow->field_id;
                $customValue->option_id = $option_id;
                $customValue->value     = $optionValue;
                $customValue->save();
            }
            elseif($catRow->Field->type == 'radio'){
                $optionValue = $request->radio;

                $fieldOption = FieldOptions::where('id', $optionValue)
                ->where('field_id', $catRow->field_id)
                ->first();

                $option_id = $fieldOption->id;

                $customValue = new AdsCustomValue();
                $customValue->ads_id    = $ad->id;
                $customValue->field_id  = $catRow->field_id;
                $customValue->option_id = $option_id;
                $customValue->value     = $optionValue;
                $customValue->save();

            }
            elseif($catRow->Field->type == 'checkbox_multiple'){

                foreach($catRow->Field->FieldOption as $fieldOptionRow){

                    if($request->$fieldOptionRow->value == 'checked'){

                        $customValue = new AdsCustomValue();
                        $customValue->ads_id    = $ad->id;
                        $customValue->field_id  = $catRow->field_id;
                        $customValue->option_id = $fieldOptionRow->id;
                        $customValue->value     = $fieldOptionRow->value;
                        $customValue->save();
                    }
                }
            }
            elseif($catRow->Field->type == 'checkbox'){

                $field_name = $catRow->Field->name;

                if($request->$field_name == 'checked'){

                    $customValue = new AdsCustomValue();
                    $customValue->ads_id    = $ad->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = 0;
                    $customValue->value     = $catRow->Field->default_value;
                    $customValue->save();
                }
            }
            elseif($catRow->Field->type == 'date'){

                $field_name = $catRow->Field->name;

                $date = Carbon::createFromFormat('d/m/Y', $request->$field_name)->format('Y-m-d');

                $customValue = new AdsCustomValue();
                $customValue->ads_id    = $ad->id;
                $customValue->field_id  = $catRow->field_id;
                $customValue->option_id = 0;
                $customValue->value     = $date;
                $customValue->save();
            }
            elseif($catRow->Field->type == 'file'){

                $field_name = $catRow->Field->name;

                $file = uniqid().'.'.$request->$field_name->getClientOriginalExtension();
            
                $request->$field_name->storeAs('public/custom_file', $file);

                $file = 'storage/custom_file/'.$file;

                $customValue = new AdsCustomValue();
                $customValue->ads_id    = $ad->id;
                $customValue->field_id  = $catRow->field_id;
                $customValue->option_id = 0;
                $customValue->value     = $file;
                $customValue->save();
            }
            else{
                $field_name = $catRow->Field->name;

                $customValue = new AdsCustomValue();
                $customValue->ads_id    = $ad->id;
                $customValue->field_id  = $catRow->field_id;
                $customValue->option_id = 0;
                $customValue->value     = $request->$field_name;
                $customValue->save();
            }
        }

        return redirect()->route('ads.index');
    }

    public function getCustomField(Request $request){

        $field = CategoryField::where('category_id', $request->id)
        ->with(['Field' => function($a){
            $a->where('delete_status', '!=', Status::DELETE)
            ->with(['FieldOption' => function($q){
                $q->where('delete_status', '!=', Status::DELETE);
            }]);
        }])
        ->get();

        return response()->json($field);
    }

    public function view($id){

        $ad = Ads::where('id', $id)
        ->first();
        
        return view('ads.ad_details', compact('ad'));
    }
}
