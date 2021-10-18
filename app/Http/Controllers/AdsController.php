<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\Ads;
use App\Models\AdsCustomValue;
use App\Models\AdsFieldDependency;
use App\Models\AdsImage;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\Country;
use App\Models\FieldOptions;
use App\Models\MakeMst;
use App\Models\MotorCustomeValues;
use App\Models\MotorFeatures;
use App\Models\Notification;
use App\Models\PropertyRendCustomeValues;
use App\Models\PropertySaleCustomeValues;
use App\Models\RejectReason;
use App\Models\SellerInformation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdsController extends Controller
{
    public function index(){

        $ad = Ads::where('delete_status', '!=', Status::DELETE)
        ->orderBy('created_at', 'desc')
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

        $user = User::where('id', Auth::user()->id)
        ->first();
        
        return view('ads.create_ad', compact('category', 'country', 'user'));
    }

    public function store(Request $request){
        
        $request->validate([
            'category'          => 'required|numeric',
            // 'title'             => 'required',
            'price'             => 'required|numeric',
            'state'             => 'required|numeric',
            // 'subcategory'       => 'numeric',
            'canonical_name'    => 'required',
            'country'           => 'required|numeric',
            // 'city'              => 'required|numeric',
            // 'description'       => 'required',
            'seller_name'       => 'required',
            'Phone'             => 'required|numeric',
            'email'             => 'required|email',
            'customer_address'  => 'required',
            'image.*'           => 'required|mimes:png,jpg,jpeg',
        ]);
        

        if(!$request->title && !$request->arabic_title){
            session()->flash('title_error', 'Please fill any of the title field');
            return redirect()->back();
        }

        if(!$request->description && !$request->description_arabic){
            session()->flash('description_error', 'Please fill any of the description field');
            return redirect()->back();
        }

        if($request->category == 1){
            $request->validate([
                'make'              => 'required|numeric',
                'model'             => 'required|numeric',
                'varient'           => 'required|numeric',
                'registered_year'   => 'required|numeric',
                'fuel'              => 'required',
                'transmission'      => 'required',
                'condition'         => 'required',
                'mileage'           => 'required|numeric',
                'features.*'        => 'required',
            ]);
        }

        elseif($request->category == 2 || $request->category == 3){
            $request->validate([
                'size'      => 'required|numeric',
                'rooms'     => 'required|numeric',
                'furnished' => 'required',
                'building'  => 'required',
            ]);
        }
        
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

        if($request->city){
            $city = $request->city;
        }
        else{
            $city = 0;
        }
        if($request->subcategory){
            $subcategory = $request->subcategory;
        }
        else{
            $subcategory = 0;
        }

        if($request->phone_hide_flag == 'checked'){
            $phoneHideFlag = 1;
        }
        else{
            $phoneHideFlag = 0;
        }

        $seller                     = new SellerInformation();
        $seller->name               = $request->seller_name;
        $seller->email              = $request->email;
        $seller->phone              = $request->Phone;
        $seller->phone_hide_flag    = $phoneHideFlag;
        $seller->address            = $request->customer_address;
        $seller->save();
        
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
        $ad->subcategory_id         = $subcategory;
        $ad->title                  = $request->title;
        $ad->canonical_name         = $request->canonical_name;
        $ad->description            = $request->description;
        $ad->price                  = $request->price;
        $ad->negotiable_flag        = $negotiable_flag;
        $ad->country_id             = $request->country;
        $ad->state_id               = $request->state;
        $ad->city_id                = $city;
        $ad->sellerinformation_id   = $seller->id;
        $ad->customer_id            = Auth()->user()->id;
        $ad->featured_flag          = $featured_flag;
        $ad->latitude               = $request->address_latitude;
        $ad->longitude              = $request->address_longitude;
        $ad->status                 = $status;
        $ad->save();

        if($request->hasFile('image')){

            foreach($request->image as $row){

                $image = uniqid().'.'.$row->getClientOriginalExtension();

                // $img = $this->imageWatermark($row, $image);

                             
                // $img->save(public_path('storage/ads', $image));
                // dd(Storage::getVisibility($image));
                // Storage::disk('public')->move(public_path('tempfile').$image, 'public/ads/'.$image);

                $row->storeAs('public/ads', $image);
                $image = 'storage/ads/'.$image;

                $adImage            = new AdsImage();
                $adImage->ads_id    = $ad->id;
                $adImage->image     = $image;
                $adImage->save();
            }
        }

        if($request->category == 1){

            $motor                      = new MotorCustomeValues();
            $motor->ads_id              = $ad->id;
            $motor->make_id             = $request->make;
            $motor->model_id            = $request->model;
            $motor->varient_id          = $request->varient;
            $motor->registration_year   = $request->registered_year;
            $motor->fuel_type           = $request->fuel;
            $motor->transmission        = $request->transmission;
            $motor->condition           = $request->condition;
            $motor->milage              = $request->mileage;
            $motor->save();

            if($request->features){

                foreach($request->features as $feature1){
                    
                    $feature            = new MotorFeatures();
                    $feature->ads_id    = $ad->id;
                    $feature->value     = $feature1;
                    $feature->save();
                }
            }

        }
        elseif($request->category == 2){

            if($request->parking == 'Parking'){
                $parking = 1;
            }
            else{
                $parking = 0;
            }

            $propery                = new PropertyRendCustomeValues();
            $propery->ads_id        = $ad->id;
            $propery->size          = $request->size;
            $propery->room          = $request->rooms;
            $propery->furnished     = $request->furnished;
            $propery->building_type = $request->building;
            $propery->parking       = $parking;
            $propery->save();
        }
        elseif($request->category == 3){

            if($request->parking == 'Parking'){
                $parking = 1;
            }
            else{
                $parking = 0;
            }

            $propery                = new PropertySaleCustomeValues();
            $propery->ads_id        = $ad->id;
            $propery->size          = $request->size;
            $propery->room          = $request->rooms;
            $propery->furnished     = $request->furnished;
            $propery->building_type = $request->building;
            $propery->parking       = $parking;
            $propery->save();

        }

        foreach($categoryField as $catRow){
            
            if($catRow->Field->type == 'select'){

                $select = $catRow->Field->name;

                if($request->$select){

                    $option_id = $request->$select;
                    
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
            }
            elseif($catRow->Field->type == 'radio'){

                $radio = $catRow->Field->name;

                if($request->$radio){

                    $optionValue = $request->$radio;
                    
                    $fieldOption = FieldOptions::where('value', $optionValue)
                    ->where('field_id', $catRow->field_id)
                    ->first();

                    $option_id = $fieldOption->id;

                    $customValue            = new AdsCustomValue();
                    $customValue->ads_id    = $ad->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = $option_id;
                    $customValue->value     = $optionValue;
                    $customValue->save();
                }

            }
            // elseif($catRow->Field->type == 'checkbox_multiple'){

            //     foreach($catRow->Field->FieldOption as $fieldOptionRow){

            //         $optionValue1 = $fieldOptionRow->value;

            //         if($request->$optionValue1 == 'checked'){

            //             $customValue = new AdsCustomValue();
            //             $customValue->ads_id    = $ad->id;
            //             $customValue->field_id  = $catRow->field_id;
            //             $customValue->option_id = $fieldOptionRow->id;
            //             $customValue->value     = $fieldOptionRow->value;
            //             $customValue->save();
            //         }
            //     }
            // }
            elseif($catRow->Field->type == 'checkbox'){

                $field_name = $catRow->Field->name;

                if($request->$field_name){

                    if($request->$field_name == 'checked'){
                        if($catRow->Field->default_value){
                            $val = $catRow->Field->default_value;
                        }
                        else{
                            $val = 1;
                        }

                        $customValue = new AdsCustomValue();
                        $customValue->ads_id    = $ad->id;
                        $customValue->field_id  = $catRow->field_id;
                        $customValue->option_id = 0;
                        $customValue->value     = $val;
                        $customValue->save();
                    }
                }
            }
            elseif($catRow->Field->type == 'date'){

                $field_name = $catRow->Field->name;

                // $date = Carbon::createFromFormat('d/m/Y', $request->$field_name)->format('Y-m-d');
                if($request->$field_name){

                    $customValue = new AdsCustomValue();
                    $customValue->ads_id    = $ad->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = 0;
                    $customValue->value     = $request->$field_name;
                    $customValue->save();
                }
            }
            elseif($catRow->Field->type == 'file'){

                $field_name = $catRow->Field->name;

                if($request->$field_name){

                    if($request->hasFile($field_name)){
                        $file = uniqid().'.'.$request->$field_name->getClientOriginalExtension();
                    
                        $request->$field_name->storeAs('public/custom_file', $file);

                        $file = 'storage/custom_file/'.$file;

                        $customValue = new AdsCustomValue();
                        $customValue->ads_id    = $ad->id;
                        $customValue->field_id  = $catRow->field_id;
                        $customValue->option_id = 0;
                        $customValue->value     = $file;
                        $customValue->file      = 1;
                        $customValue->save();
                    }
                }
            }
            elseif($catRow->Field->type == 'dependency'){

            }
            else{
                $field_name = $catRow->Field->name;

                if($request->$field_name){

                    $customValue = new AdsCustomValue();
                    $customValue->ads_id    = $ad->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = 0;
                    $customValue->value     = $request->$field_name;
                    $customValue->save();
                }
            }
        }

        if($request->Make){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'make';
            $adsDependency->master_id   = $request->Make;
            $adsDependency->save();
        }

        if($request->Model){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'model';
            $adsDependency->master_id   = $request->Model;
            $adsDependency->save();
        }

        if($request->Variant){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'variant';
            $adsDependency->master_id   = $request->Variant;
            $adsDependency->save();
        }

        if($request->Country){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'country';
            $adsDependency->master_id   = $request->Country;
            $adsDependency->save();
        }

        if($request->State){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'state';
            $adsDependency->master_id   = $request->State;
            $adsDependency->save();
        }

        if($request->City){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'city';
            $adsDependency->master_id   = $request->City;
            $adsDependency->save();
        }

        session()->flash('success', 'Ad has been created');
        return redirect()->route('ads.index');
    }

    public function view($id){

        $ad = Ads::where('id', $id)
        ->first();
        
        return view('ads.ad_details', compact('ad'));
    }

    public function edit($id){

        $ad = Ads::where('id', $id)
        ->first();

        $category = Category::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->get();

        $country = Country::orderBy('name')
        ->get();

        return view('ads.edit_ad', compact('ad', 'category', 'country'));
    }

    public function update(Request $request, $id){
        
        $request->validate([
            'category'          => 'required|numeric',
            // 'title'             => 'required',
            'price'             => 'required|numeric',
            'state'             => 'required|numeric',
            // 'subcategory'       => 'numeric',
            'canonical_name'    => 'required',
            'country'           => 'required|numeric',
            // 'city'              => 'required|numeric',
            'description'       => 'required',
            'seller_name'       => 'required',
            'phone'             => 'required|numeric',
            'email'             => 'required|email',
            'customer_address'  => 'required',
            'image.*'           => 'mimes:png,jpg,jpeg',
        ]);

        if($request->category == 1){
            $request->validate([
                'make'              => 'required|numeric',
                'model'             => 'required|numeric',
                'varient'           => 'required|numeric',
                'registered_year'   => 'required|numeric',
                'fuel'              => 'required',
                'transmission'      => 'required',
                'condition'         => 'required',
                'mileage'           => 'required|numeric',
                'features.*'        => 'required',
            ]);
        }

        elseif($request->category == 2 || $request->category == 3){
            $request->validate([
                'size'      => 'required|numeric',
                'rooms'     => 'required|numeric',
                'furnished' => 'required',
                'building'  => 'required',
            ]);
        }


        $ad = Ads::where('id', $id)
        ->first();

        AdsCustomValue::where('ads_id', $id)
        ->delete();

        AdsFieldDependency::where('ads_id', $id)
        ->update([
            'delete_status' => Status::DELETE,
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

        if($request->city){
            $city = $request->city;
        }
        else{
            $city = 0;
        }
        if($request->subcategory){
            $subcategory = $request->subcategory;
        }
        else{
            $subcategory = 0;
        }
        if($request->phone_hide_flag == 'checked'){
            $phoneHideFlag = 1;
        }
        else{
            $phoneHideFlag = 0;
        }

        
        $categoryField = CategoryField::where('category_id', $request->category)
        ->with(['Field' => function($a){
            $a->where('delete_status', '!=', Status::DELETE)
            ->with(['FieldOption' => function($q){
                $q->where('delete_status', '!=', Status::DELETE);
            }]);
        }])
        ->get();

        SellerInformation::where('id', $ad->sellerinformation_id)
        ->update([
            'name'              => $request->seller_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'phone_hide_flag'   => $phoneHideFlag,
            'address'           => $request->customer_address,
        ]);

        Ads::where('id', $id)
        ->update([
            'category_id'       => $request->category,
            'subcategory_id'    => $subcategory,
            'title'             => $request->title,
            'canonical_name'    => $request->canonical_name,
            'description'       => $request->description,
            'price'             => $request->price,
            'negotiable_flag'   => $negotiable_flag,
            'country_id'        => $request->country,
            'state_id'          => $request->state,
            'city_id'           => $city,
            'featured_flag'     => $featured_flag,
            'latitude'          => $request->address_latitude,
            'longitude'         => $request->address_longitude,
            'status'            => $status,
        ]);

        if($request->category == 1){

            MotorCustomeValues::where('ads_id', $id)
            ->update([
                'make_id'           => $request->make,
                'model_id'          => $request->model,
                'varient_id'        => $request->varient,
                'registration_year' => $request->registered_year,
                'fuel_type'         => $request->fuel,
                'transmission'      => $request->transmission,
                'condition'         => $request->condition,
                'milage'            => $request->mileage,
            ]);

            MotorFeatures::where('ads_id', $id)
            ->delete();

            if($request->features){

                foreach($request->features as $feature1){
                    
                    $feature            = new MotorFeatures();
                    $feature->ads_id    = $ad->id;
                    $feature->value     = $feature1;
                    $feature->save();
                }
            }
        }
        elseif($request->category == 2){

            if($request->parking == 'Parking'){
                $parking = 1;
            }
            else{
                $parking = 0;
            }

            PropertyRendCustomeValues::where('ads_id', $id)
            ->update([
                'size'          => $request->size,
                'room'          => $request->rooms,
                'furnished'     => $request->furnished,
                'building_type' => $request->building,
                'parking'       => $parking,
            ]);
        }
        elseif($request->category == 3){

            if($request->parking == 'Parking'){
                $parking = 1;
            }
            else{
                $parking = 0;
            }

            PropertySaleCustomeValues::where('ads_id', $id)
            ->update([
                'size'          => $request->size,
                'room'          => $request->rooms,
                'furnished'     => $request->furnished,
                'building_type' => $request->building,
                'parking'       => $parking,
            ]);

        }

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
                $select = $catRow->Field->name;

                if($request->$select){

                    $option_id = $request->$select;
                    
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
            }
            elseif($catRow->Field->type == 'radio'){
                $radio = $catRow->Field->name;

                if($request->$radio){
                    $optionValue = $request->$radio;
                    
                    $fieldOption = FieldOptions::where('value', $optionValue)
                    ->where('field_id', $catRow->field_id)
                    ->first();

                    $option_id = $fieldOption->id;

                    $customValue            = new AdsCustomValue();
                    $customValue->ads_id    = $ad->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = $option_id;
                    $customValue->value     = $optionValue;
                    $customValue->save();
                }

            }
            // elseif($catRow->Field->type == 'checkbox_multiple'){

            //     foreach($catRow->Field->FieldOption as $fieldOptionRow){

            //         $optionValue1 = $fieldOptionRow->value;

            //         if($request->$optionValue1 == 'checked'){

            //             $customValue = new AdsCustomValue();
            //             $customValue->ads_id    = $ad->id;
            //             $customValue->field_id  = $catRow->field_id;
            //             $customValue->option_id = $fieldOptionRow->id;
            //             $customValue->value     = $fieldOptionRow->value;
            //             $customValue->save();
            //         }
            //     }
            // }
            elseif($catRow->Field->type == 'checkbox'){

                $field_name = $catRow->Field->name;

                if($request->$field_name){
                    if($request->$field_name == 'checked'){
                        if($catRow->Field->default_value){
                            $val = $catRow->Field->default_value;
                        }
                        else{
                            $val = 1;
                        }

                        $customValue = new AdsCustomValue();
                        $customValue->ads_id    = $ad->id;
                        $customValue->field_id  = $catRow->field_id;
                        $customValue->option_id = 0;
                        $customValue->value     = $val;
                        $customValue->save();
                    }
                }
            }
            elseif($catRow->Field->type == 'date'){

                $field_name = $catRow->Field->name;

                // $date = Carbon::createFromFormat('d/m/Y', $request->$field_name)->format('Y-m-d');

                if($request->$field_name){

                    $customValue = new AdsCustomValue();
                    $customValue->ads_id    = $ad->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = 0;
                    $customValue->value     = $request->$field_name;
                    $customValue->save();
                }
            }
            elseif($catRow->Field->type == 'file'){

                $field_name = $catRow->Field->name;

                if($request->$field_name){

                    if($request->hasFile($field_name)){

                        $file = uniqid().'.'.$request->$field_name->getClientOriginalExtension();
                    
                        $request->$field_name->storeAs('public/custom_file', $file);

                        $file = 'storage/custom_file/'.$file;

                        $customValue = new AdsCustomValue();
                        $customValue->ads_id    = $ad->id;
                        $customValue->field_id  = $catRow->field_id;
                        $customValue->option_id = 0;
                        $customValue->value     = $file;
                        $customValue->file      = 1;
                        $customValue->save();
                    }
                }
            }
            elseif($catRow->Field->type == 'dependency'){

            }
            else{
                $field_name = $catRow->Field->name;

                if($request->field_name){
                    $customValue = new AdsCustomValue();
                    $customValue->ads_id    = $ad->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = 0;
                    $customValue->value     = $request->$field_name;
                    $customValue->save();
                }
            }
        }

        if($request->Make){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'make';
            $adsDependency->master_id   = $request->Make;
            $adsDependency->save();
        }

        if($request->Model){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'model';
            $adsDependency->master_id   = $request->Model;
            $adsDependency->save();
        }

        if($request->Variant){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'variant';
            $adsDependency->master_id   = $request->Variant;
            $adsDependency->save();
        }

        if($request->Country){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'country';
            $adsDependency->master_id   = $request->Country;
            $adsDependency->save();
        }

        if($request->State){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'state';
            $adsDependency->master_id   = $request->State;
            $adsDependency->save();
        }

        if($request->City){

            $adsDependency              = new AdsFieldDependency();
            $adsDependency->ads_id      = $ad->id;
            $adsDependency->master_type = 'city';
            $adsDependency->master_id   = $request->City;
            $adsDependency->save();
        }

        session()->flash('success', 'Ad has been created');
        return redirect()->route('ads.index');

    }

    public function delete($id){

        Ads::where('id', $id)
        ->update([
            'delete_status' => Status::DELETE,
        ]);

        session()->flash('success', 'Ad has been deleted');
        return redirect()->route('ads.index');
    }

    public function getAdsRelated(Request $request){

        $customValue = AdsCustomValue::where('ads_id', $request->id)
        ->orderBy('field_id')
        ->get()
        ->map(function($a){
            $a->type = $a->Field->type;
            $a->field_name = $a->Field->name;
            unset($a->Field);
            return $a;
        });

        $adsDependency = AdsFieldDependency::where('ads_id', $request->id)
        ->get()
        ->map(function($a){

            if($a->master_type == 'make'){
                $a->name = $a->Make->name;
            }
            if($a->master_type == 'model'){
                $a->name = $a->Model->name;
            }
            if($a->master_type == 'Variant'){
                $a->name = $a->Variant->name;
            }

            unset($a->Make, $a->Model);
            return $a;
        });

        return response()->json([
            'customValue'   => $customValue,
            'adsDependency' => $adsDependency,
        ], 200);
    }

    public function getCustomField(Request $request){

        $field = CategoryField::where('category_id', $request->id)
        ->orderBy('field_id')
        ->with(['Field' => function($a){
            $a->where('delete_status', '!=', Status::DELETE)
            ->where(function($b){
                $b->orwhere(function($c){
                    $c->where('option', 1)
                    ->whereHas('FieldOption', function($e){
                        $e->where('delete_status', '!=', Status::DELETE)
                        ->where('status', Status::ACTIVE);
                    });
                })
                ->orwhere(function($d){
                    $d->where('option', 2)
                    ->wherehas('Dependency', function($f){
                        $f->where('delete_status', '!=', Status::DELETE);
                    });
                })
                ->orwhere(function($d){
                    $d->where('option', 0);
                });
            });
            // ->with(['FieldOption' => function($q){
            //     $q->where('delete_status', '!=', Status::DELETE);
            // }])
            // ->with(['Dependency' => function($r){
            //     $r->where('delete_status', '!=', Status::DELETE)
            //     ->orderBy('order')
            //     ->groupBy('field_id');
            // }]);
        }])
        ->whereHas('Field', function($a){
            $a->where('delete_status', '!=', Status::DELETE)
            ->where(function($b){
                $b->orwhere(function($c){
                    $c->where('option', 1)
                    ->whereHas('FieldOption', function($e){
                        $e->where('delete_status', '!=', Status::DELETE)
                        ->where('status', Status::ACTIVE);
                    });
                })
                ->orwhere(function($d){
                    $d->where('option', 2)
                    ->wherehas('Dependency', function($f){
                        $f->where('delete_status', '!=', Status::DELETE);
                    });
                })
                ->orwhere(function($d){
                    $d->where('option', 0);
                });
            });
        })
        ->get()
        ->map(function($p){
            
            if($p->Field){
                if($p->Field->option == 1){
                    $p->Field->FieldOption;
                }
                elseif($p->Field->option == 2){
                    $p->Field->Dependency;
                }
                else{
                    $p->Field;
                }
            }

            return $p;
        });
        
        return response()->json($field);
    }

    public function getMasterDependency(Request $request){

        if($request->master == 'Make'){

            $dependency = MakeMst::where('status', Status::ACTIVE)
            ->orderBy('sort_order')
            ->get();
        }
        elseif($request->master == 'Country'){

            $dependency = Country::orderBy('name')
            ->get();
        }

        return response()->json($dependency);
    }

    public function adAccept($id){
        
        Ads::where('id', $id)
        ->update([
            'status'    => Status::ACTIVE,
        ]);

        session()->flash('success', 'Ad has been accepted');
        return redirect()->route('ads.index');
    }

    public function getRejectReson(Request $request){

        $reason = RejectReason::where('id', $request->id)
        ->first();

        return response()->json($reason);
    }

    public function adRequestIndex(){

        $adsRequest = Ads::where('status', Status::REQUEST)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        $reason = RejectReason::where('status', Status::ACTIVE)
        ->where('type', 0)
        ->orderBy('reson')
        ->get();

        $refund = RejectReason::where('status', Status::ACTIVE)
        ->where('type', 1)
        ->orderBy('reson')
        ->get();

        $adsRejected = Ads::where('status', Status::REJECTED)
        ->orderBy('created_at', 'desc')
        ->paginate(10, '*', 'rejected');

        $adsRefund = Ads::where('status', Status::REFUND)
        ->orderBy('created_at', 'desc')
        ->paginate(10, '*', 'refund');

        return view('ads.request.ad_request', compact('adsRequest', 'reason', 'adsRejected', 'refund', 'adsRefund'));
    }

    public function adRequestDetails($id){

        $ad = Ads::where('id', $id)
        ->first();

        $reason = RejectReason::where('status', Status::ACTIVE)
        ->orderBy('reson')
        ->get();

        return view('ads.request.request_details', compact('ad', 'reason'));
    }

    public function adReject(Request $request){

        Ads::where('id', $request->ad_id)
        ->update([
            'status'            => Status::REJECTED,
            'reject_reason_id'  => $request->reason,
        ]);

        session()->flash('success', 'Ad has been rejected');
        return redirect()->route('ad_request.index');
    }

    public function getMotorFeature(Request $request){

        $request->validate([

            'id'    => 'required',
        ]);

        $feature = MotorFeatures::where('ads_id', $request->id)
        ->get();

        return response()->json($feature);
    }

    public function adRefund(Request $request){

        Ads::where('id', $request->ad_id)
        ->update([
            'status'            => Status::REFUND,
            'reject_reason_id'  => $request->reason,
        ]);

        session()->flash('success', 'Ad refund initiated');
        return redirect()->route('ad_request.index');
    }

    public function adNotification(){

        // $ad = Ads::where('notification_status', 0)
        // ->count();

        $notification = Notification::where('status', 0)
        ->get();

        return response()->json($notification);
    }

    public function readNotification(){

        Ads::where('notification_status', 0)
        ->update([
            'notification_status'   => 1,
        ]);

        Notification::where('status', 0)
        ->update([
            'status'    => 1,
        ]);
    }



    public static function imageWatermark($image, $fileName){

        // $img = Image::make($image);

        // $newImg = Image::make(public_path('brand.png'));

        // $newImg->resize(113, 24);
        
        // $newImg->opacity(50);

        // $img->insert($newImg, 'center-left', 10, 10);

        // $img->save(public_path('tempfile/'.$fileName));

        // return $img;
    }
}
