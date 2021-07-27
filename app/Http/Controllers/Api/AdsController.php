<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\AdsCustomValue;
use App\Models\AdsFieldDependency;
use App\Models\AdsImage;
use App\Models\CategoryField;
use App\Models\City;
use App\Models\Country;
use App\Models\FieldOptions;
use App\Models\MakeMst;
use App\Models\ModelMst;
use App\Models\SellerInformation;
use App\Models\State;
use App\Models\VarientMst;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdsController extends Controller
{
    public function adStore(Request $request){
        
        $rules = [
            'category'          => 'required|numeric',
            'subcategory'       => 'required|numeric',
            'title'             => 'required',
            'canonical_name'    => 'required',
            'description'       => 'required',
            'price'             => 'required|numeric',
            'country'           => 'required|numeric',
            'state'             => 'required|numeric',
            'city'              => 'required|numeric',
            'latitude'          => 'required|numeric',
            'longitude'         => 'required|numeric',
            'image.*'           => 'required|mimes:jpg,png,jpeg',
            'name'              => 'required',
            'email'             => 'required|email',
            'phone'             => 'required',
            'address'           => 'required'
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid request',
                'errors'    => $validate->errors(),
            ], 400);
        }

        // try{

            $categoryField = CategoryField::where('category_id', $request->category)
            ->with(['Field' => function($a){
                $a->where('delete_status', '!=', Status::DELETE)
                ->with(['FieldOption' => function($q){
                    $q->where('delete_status', '!=', Status::DELETE);
                }]);
            }])
            ->get();

            if($request->phone_hide == true){
                $phone_hide = Status::ACTIVE;
            }
            else{
                $phone_hide = Status::INACTIVE;
            }

            if($request->negotiable == true){
                $negotiable = Status::ACTIVE;
            }
            else{
                $negotiable = Status::INACTIVE;
            }

            if($request->featured == true){
                $featured = Status::ACTIVE;
            }
            else{
                $featured = Status::INACTIVE;
            }

            $customer                   = new SellerInformation();
            $customer->name             = $request->name;
            $customer->email            = $request->email;
            $customer->phone            = $request->phone;
            $customer->phone_hide_flag  = $phone_hide;
            $customer->address          = $request->address;
            $customer->save();

            $ads                        = new Ads();
            $ads->category_id           = $request->category;
            $ads->subcategory_id        = $request->subcategory;
            $ads->title                 = $request->title;
            $ads->canonical_name        = $request->canonical_name;
            $ads->description           = $request->description;
            $ads->price                 = $request->price;
            $ads->negotiable_flag       = $negotiable;
            $ads->country_id            = $request->country;
            $ads->state_id              = $request->state;
            $ads->city_id               = $request->city;
            $ads->sellerinformation_id  = $customer->id;
            $ads->customer_id           = 1;//Auth::user()->id;
            $ads->payment_id            = 0;
            $ads->featured_flag         = $featured;
            $ads->latitude              = $request->latitude;
            $ads->longitude             = $request->longitude;
            $ads->status                = Status::REQUEST;
            $ads->save();

            if($request->hasFile('image')){

                foreach($request->image as $row){

                    $image = uniqid().'.'.$row->getClientOriginalExtension();
                
                    $row->storeAs('public/ads', $image);

                    $image = 'storage/ads/'.$image;

                    $adImage            = new AdsImage();
                    $adImage->ads_id    = $ads->id;
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
                    $customValue->ads_id    = $ads->id;
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
                    $customValue->ads_id    = $ads->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = $option_id;
                    $customValue->value     = $optionValue;
                    $customValue->save();
    
                }
                elseif($catRow->Field->type == 'checkbox_multiple'){
    
                    foreach($catRow->Field->FieldOption as $fieldOptionRow){
    
                        $optionRow1 = $fieldOptionRow->value;
                        
                        if($request->$optionRow1 == true){
    
                            $customValue = new AdsCustomValue();
                            $customValue->ads_id    = $ads->id;
                            $customValue->field_id  = $catRow->field_id;
                            $customValue->option_id = $fieldOptionRow->id;
                            $customValue->value     = $fieldOptionRow->value;
                            $customValue->save();
                        }
                    }
                }
                elseif($catRow->Field->type == 'checkbox'){
    
                    $field_name = $catRow->Field->name;
    
                    if($request->$field_name == true){
    
                        $customValue = new AdsCustomValue();
                        $customValue->ads_id    = $ads->id;
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
                    $customValue->ads_id    = $ads->id;
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
                    $customValue->ads_id    = $ads->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = 0;
                    $customValue->value     = $file;
                    $customValue->save();
                }
                elseif($catRow->Field->type == 'dependency'){
    
                }
                else{
                    $field_name = $catRow->Field->name;
    
                    $customValue = new AdsCustomValue();
                    $customValue->ads_id    = $ads->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = 0;
                    $customValue->value     = $request->$field_name;
                    $customValue->save();
                }
            }
    
            if($request->Make){
    
                $adsDependency              = new AdsFieldDependency();
                $adsDependency->ads_id      = $ads->id;
                $adsDependency->master_type = 'make';
                $adsDependency->master_id   = $request->Make;
                $adsDependency->save();
            }
    
            if($request->Model){
    
                $adsDependency              = new AdsFieldDependency();
                $adsDependency->ads_id      = $ads->id;
                $adsDependency->master_type = 'model';
                $adsDependency->master_id   = $request->Model;
                $adsDependency->save();
            }
    
            if($request->Variant){
    
                $adsDependency              = new AdsFieldDependency();
                $adsDependency->ads_id      = $ads->id;
                $adsDependency->master_type = 'variant';
                $adsDependency->master_id   = $request->Variant;
                $adsDependency->save();
            }
    
            if($request->Country){
    
                $adsDependency              = new AdsFieldDependency();
                $adsDependency->ads_id      = $ads->id;
                $adsDependency->master_type = 'country';
                $adsDependency->master_id   = $request->Country;
                $adsDependency->save();
            }
    
            if($request->State){
    
                $adsDependency              = new AdsFieldDependency();
                $adsDependency->ads_id      = $ads->id;
                $adsDependency->master_type = 'state';
                $adsDependency->master_id   = $request->State;
                $adsDependency->save();
            }
    
            if($request->City){
    
                $adsDependency              = new AdsFieldDependency();
                $adsDependency->ads_id      = $ads->id;
                $adsDependency->master_type = 'city';
                $adsDependency->master_id   = $request->City;
                $adsDependency->save();
            }

            return response()->json([
                'status'    => 'success',
                'message'   => 'Ad request has been placed',
            ], 200);

        // }
        // catch (\Exception $e) {
            

        //     return response()->json([
        //         'status'    => 'error',
        //         'message'   => 'Something went wrong',
        //     ], 301);
        // }

    }

    public function customFieldsAndDependency(Request $request){

        $rules = [
            'category'      => 'required|numeric',
            'subcategory'   => 'required|numeric',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid request',
                'errors'    => $validate->errors(),
            ], 400);
        }

        try{

            $category_field = CategoryField::where('delete_status', '!=', Status::DELETE)
            ->where('category_id', $request->category)
            ->with(['Field' => function($a){
                $a->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE);
            }])
            ->get()
            ->map(function($a){
                
                    
                    if($a->Field->description_area_flag == 0){
                        $a->Field->position = 'top';
                    }
                    elseif($a->Field->description_area_flag == 1){
                        $a->Field->position = 'details_page';
                    }
                    else{
                        $a->Field->position = 'none';
                    }

                    if($a->Field->option == 1){
                        $a->Field->FieldOption->map(function($b){
                            
                            unset($b->field_id, $b->delete_status);
                            return $b;
                        });
                    }
                    elseif($a->Field->option == 2){
                        $a->Field->Dependency->map(function($c){
                            
                            unset($c->delete_status, $c->field_id);
                            return $c;
                        });
                    }

                    unset($a->delete_status, $a->field_id, $a->Field->status, $a->Field->delete_status);
                    
                return $a;
            });

            return response()->json([
                'status'    => 'success',
                'data'      => [
                    'category_field'    => $category_field,
                ],
            ], 200);
        }
        catch (\Exception $e) {
            

            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getMasterDependency(Request $request){

        $rules = [
            'master'    => 'required',
            'master_id' => 'numeric',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid request',
                'errors'    => $validate->errors(),
            ], 400);
        }

        try {
            
            if($request->master == 'Make'){

                $dependency = MakeMst::where('status', Status::ACTIVE)
                ->orderBy('sort_order')
                ->get();
            }
            elseif($request->master == 'Model'){

                $dependency = ModelMst::where('status', Status::ACTIVE)
                ->where('make_id', $request->master_id)
                ->orderBy('sort_order')
                ->get();
            }
            elseif($request->master == 'Varient'){

                $dependency = VarientMst::where('status', Status::ACTIVE)
                ->where('model_id', $request->master_id)
                ->orderBy('order')
                ->get();
            }
            elseif($request->master == 'Country'){

                $dependency = Country::orderBy('name')
                ->get();
            }
            elseif($request->master == 'State'){

                $dependency = State::orderBy('name')
                ->where('country_id', $request->master_id)
                ->get();
            }
            elseif($request->master == 'City'){

                $dependency = City::orderBy('name')
                ->where('state_id', $request->master_id)
                ->get();
            }

            return response()->json([
                'status'        => 'success',
                'message'       => 'Master data found',
                'mster_data'    => $dependency,
            ], 200);
        }
        catch (\Exception $e) {
            
    
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }
}
