<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\AdsCustomValue;
use App\Models\AdsFieldDependency;
use App\Models\AdsImage;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\City;
use App\Models\Country;
use App\Models\FieldOptions;
use App\Models\MakeMst;
use App\Models\ModelMst;
use App\Models\SellerInformation;
use App\Models\State;
use App\Models\Subcategory;
use App\Models\Testimonial;
use App\Models\VarientMst;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class AdsController extends Controller
{
    public function adView(Request $request){

        $rules = [
            'ads_id'    => 'required|numeric',
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

            $ads = Ads::where('id', $request->ads_id)
            ->get()
            ->map(function($a){

                if($a->category_id == 1){
                    $a->MotoreValue;
                    $a->make = $a->MotoreValue->Make->name;
                    $a->model = $a->MotoreValue->Model->name;
                    $a->MotorFeatures;

                    unset($a->MotoreValue->Make, $a->MotoreValue->Model);
                }
                elseif($a->category_id == 2){
                    $a->PropertyRend;
                }
                elseif($a->category_id ==3){
                    $a->PropertySale;
                }
                $a->image = array_filter([
                    $a->Image->map(function($q) use($a){
                        $q->image;
                        unset($q->ads_id, $q->img_flag);
                        return $q;
                    }),
                ]);

                $a->created_on = date('d-M-Y', strtotime($a->created_at));
                $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                $a->country_name = $a->Country->name;
                $a->state_name = $a->State->name;
                if($a->city_id != 0){
                    $a->city_name = $a->City->name;
                }
                else{
                    $a->city_name = $a->State->name;
                }
                $a->CustomValue->map(function($c){
                    
                    if($c->Field->description_area_flag == 0){
                        $c->position = 'top';
                        $c->name = $c->Field->name;
                    }
                    elseif($c->Field->description_area_flag == 1){
                        $c->position = 'details_page';
                        $c->name = $c->Field->name;
                    }
                    else{
                        $c->position = 'none';
                        $c->name = $c->Field->name;
                    }
                    unset($c->Field, $c->ads_id, $c->option_id, $c->field_id);
                    return $c;
                });
                $a->SellerInformation;

                unset($a->status, $a->reject_reason_id, $a->delete_status, $a->Country, $a->State, $a->City);
                return $a;
            });

            return response()->json([
                'status'    => 'success',
                'message'   => 'Ad details',
                'ads'       => $ads,
            ], 200);

        }
        catch (\Exception $e) {
            

            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function adStore(Request $request){
        
        $rules = [
            'category'          => 'required|numeric',
            // 'subcategory'       => 'required|numeric',
            'title'             => 'required',
            'canonical_name'    => 'required',
            'description'       => 'required',
            'price'             => 'required|numeric',
            'country'           => 'required|numeric',
            'state'             => 'required|numeric',
            // 'city'              => 'required|numeric',
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
                $phone_hide = 0;
            }

            if($request->negotiable == true){
                $negotiable = Status::ACTIVE;
            }
            else{
                $negotiable = 0;
            }

            if($request->featured == true){
                $featured = Status::ACTIVE;
            }
            else{
                $featured = 0;
            }

            if($request->city){
                $city = $request->city;
            }
            else{
                $city = 0;
            }

            $customer                   = new SellerInformation();
            $customer->name             = $request->name;
            $customer->email            = $request->email;
            $customer->phone            = $request->phone;
            $customer->phone_hide_flag  = $phone_hide;
            $customer->address          = $request->address;
            $customer->save();

            $ad                        = new Ads();
            $ad->category_id           = $request->category;
            $ad->subcategory_id        = $request->subcategory;
            $ad->title                 = $request->title;
            $ad->canonical_name        = $request->canonical_name;
            $ad->description           = $request->description;
            $ad->price                 = $request->price;
            $ad->negotiable_flag       = $negotiable;
            $ad->country_id            = $request->country;
            $ad->state_id              = $request->state;
            $ad->city_id               = $city;
            $ad->sellerinformation_id  = $customer->id;
            $ad->customer_id           = Auth::user()->id;
            $ad->payment_id            = 0;
            $ad->featured_flag         = $featured;
            $ad->latitude              = $request->latitude;
            $ad->longitude             = $request->longitude;
            $ad->status                = Status::REQUEST;
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
                    $select = $catRow->Field->name;
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
                elseif($catRow->Field->type == 'radio'){
                    $radio = $catRow->Field->name;
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
                elseif($catRow->Field->type == 'date'){
    
                    $field_name = $catRow->Field->name;
    
                    // $date = Carbon::createFromFormat('d/m/Y', $request->$field_name)->format('Y-m-d');
    
                    $customValue = new AdsCustomValue();
                    $customValue->ads_id    = $ad->id;
                    $customValue->field_id  = $catRow->field_id;
                    $customValue->option_id = 0;
                    $customValue->value     = $request->$field_name;
                    $customValue->save();
                }
                elseif($catRow->Field->type == 'file'){
    
                    $field_name = $catRow->Field->name;
    
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
                elseif($catRow->Field->type == 'dependency'){
    
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

                            if($c->master == 'Make'){
                                $c->option = $this->RetriveMaster('Make');
                            }
                            elseif($c->master == 'Country'){
                                $c->option = $this->RetriveMaster('Country');
                            }

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
            elseif($request->master == 'Variant'){

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

    public function RetriveMaster($master){
        if($master == "Make"){

            $masterMst = MakeMst::orderBy('sort_order')
            ->get();
        }
        elseif($master == 'Country'){
            $masterMst = Country::orderBy('name')
            ->get();
        }

        return $masterMst;
    }

    public function getCategoryMotors(){

        try{
            $motors = Category::where('id', 1)
            ->with(['Subcategory' => function($a){
                $a->withCount('Ads');
            }])
            ->first();

            $ads = Ads::where('category_id', 1)
            ->where('status', Status::ACTIVE)
            ->where('delete_status', '!=', Status::DELETE)
            ->where('featured_flag', 1)
            ->get()
            ->map(function($a){
                $a->state_name = $a->State->name;
                $a->city_name = $a->City->name;
                $a->make = $a->MotoreValue->Make->name;
                $a->model = $a->MotoreValue->Model->name;
                $a->year = $a->MotoreValue->registration_year;
                $a->milage = $a->MotoreValue->milage;
                $a->image = $a->Image;
                unset($a->MotoreValue, $a->State, $a->City);
                return $a;
            });

            $testimonial = Testimonial::orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'status'    => 'success',
                'message'   => 'category list',
                'data'      => [
                    'motors'        => $motors,
                    'ads'           => $ads,
                    'testimonial'   => $testimonial,
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

    public function getProperty(Request $request){

        $rules = [
            'category_id'   => 'required|numeric',
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

            $property = Category::where('id', $request->category_id)
            ->with(['Subcategory' => function($a){
                $a->withCount('Ads');
            }])
            ->first();

            $subcategory = Subcategory::where('category_id', $request->category_id)
            ->where('status', Status::ACTIVE)
            ->where('delete_status', '!=', Status::DELETE)
            ->orderBy('sort_order')
            ->get()
            ->map(function($a){

                $a->Ads->map(function($b){
                    
                    if($b->category_id == 2 && $b->PropertyRend){
                        
                        $b->size = $b->PropertyRend->size;
                        $b->room = $b->PropertyRend->room;
                        $b->furnished = $b->PropertyRend->furnished; 
                        $b->building_type = $b->PropertyRend->building_type;
                        $b->parking = $b->PropertyRend->parking == 0 ? 'No' : 'Yes';

                        unset($b->PropertyRend);
                    }
                    elseif($b->category_id == 3 && $b->PropertySale){
                        
                        $b->size = $b->PropertySale->size;
                        $b->room = $b->PropertySale->room;
                        $b->furnished = $b->PropertySale->furnished; 
                        $b->building_type = $b->PropertySale->building_type;
                        $b->parking = $b->PropertySale->parking == 0 ? 'No' : 'Yes';

                        unset($b->PropertySale);
                    }

                    $b->state_name = $b->State->name;
                    $b->city_name = $b->City->name;
                    $b->Image;

                    unset($b->State, $b->City);
                    return $b;
                });
                return $a;
            });

            return response()->json([
                'status'    => 'success',
                'message'   => 'category list',
                'data'      => [
                    'property'      => $property,
                    'subcategory'   => $subcategory,
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
}
