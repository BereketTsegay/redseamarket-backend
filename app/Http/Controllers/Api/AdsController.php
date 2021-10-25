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
use App\Models\Favorite;
use App\Models\FieldOptions;
use App\Models\Fields;
use App\Models\MakeMst;
use App\Models\ModelMst;
use App\Models\MotorCustomeValues;
use App\Models\MotorFeatures;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PropertyRendCustomeValues;
use App\Models\PropertySaleCustomeValues;
use App\Models\SellerInformation;
use App\Models\State;
use App\Models\Subcategory;
use App\Models\Testimonial;
use App\Models\VarientMst;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
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
                'code'      => '400',
                'errors'    => $validate->errors(),
            ], 200);
        }

        try{

            $ads = Ads::where('id', $request->ads_id)
            ->get()
            ->map(function($a){

                if($a->category_id == 1){
                    $a->MotoreValue;
                    if($a->MotoreValue){
                        $a->make = $a->MotoreValue->Make ? $a->MotoreValue->Make->name : '';
                        $a->model = $a->MotoreValue->Model ? $a->MotoreValue->Model->name : '';
                        $a->variant = $a->MotoreValue->Variant ? $a->MotoreValue->Variant->name : '';

                        unset($a->MotoreValue->Make, $a->MotoreValue->Model, $a->MotoreValue->Variant);
                    }
                    $a->MotorFeatures;

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

                $a->Payment;

                $a->country_name = $a->Country->name;
                $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
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

                if($a->sellerinformation_id == 0){
                    $a->seller = 'admin';
                }
                else{
                    $a->seller = 'user';
                }

                $a->SellerInformation;

                unset($a->reject_reason_id, $a->delete_status, $a->Country, $a->State, $a->City);
                return $a;
            });

            return response()->json([
                'status'    => 'success',
                'message'   => 'Ad details',
                'code'      => 200,
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
        
        try{
            
            $rules = [
                'category'          => 'required|numeric',
                // 'subcategory'       => 'required|numeric',
                // 'title'             => 'required',
                'canonical_name'    => 'required',
                // 'description'       => 'required',
                'price'             => 'required|numeric',
                'country'           => 'required|numeric',
                'state'             => 'required|numeric',
                // 'city'              => 'required|numeric',
                'latitude'          => 'required|numeric',
                'longitude'         => 'required|numeric',
                'image.*'           => 'required',
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
                    'code'      => 400,
                    'errors'    => $validate->errors(),
                ], 200);
            }

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

            if(isset($request->city)){
                $city = $request->city;
            }
            else{
                $city = 0;
            }

            if(isset($request->subcategory)){
                $subcategory = $request->subcategory;
            }
            else{
                $subcategory = 0;
            }

            if(!$request->titleinArabic && !$request->title){
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Please enter title in any one of the language',
                    'code'      => 400,
                ], 200);
            }

            if(!$request->descriptioninArabic && !$request->description){
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Please enter description in any one of the language',
                    'code'      => 400,
                ], 200);
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
            $ad->subcategory_id        = $subcategory;
            $ad->title                 = $request->title;
            $ad->title_arabic          = $request->titleinArabic;
            $ad->canonical_name        = $request->canonical_name;
            $ad->description           = $request->description;
            $ad->description_arabic    = $request->descriptioninArabic;
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
            $ad->notification_status   = 0;
            $ad->save();

            if($featured == Status::ACTIVE){

                if($request->paymentMethod == 'stripe'){

                    Payment::where('payment_id', $request->paymentId)
                    ->update([
                        'ads_id'    => $ad->id,
                    ]);
                }
                else{

                    $subcategory = Subcategory::where('id', $request->subcategory)
                    ->first();

                    if($subcategory->type == 0){
                        $amount = $subcategory->percentage;
                    }
                    else{
                        $amount = $request->price * ($subcategory->percentage / 100);
                    }

                    $payment                = new Payment();
                    $payment->payment_id    = $ad->id . uniqid();
                    $payment->amount        = $amount;
                    $payment->ads_id        = $ad->id;
                    $payment->name          = $request->name;
                    $payment->email         = $request->email;
                    $payment->phone         = $request->phone;
                    $payment->payment_type  = 1; // 1 for Payment through account or direct
                    $payment->status        = 'Payment pending';
                    $payment->save();
                }
            }

            if($request->category == 1){
                $motor                      = new MotorCustomeValues();
                $motor->ads_id              = $ad->id;
                $motor->make_id             = $request->make_id;
                $motor->model_id            = $request->model_id;
                $motor->varient_id          = $request->variant_id;
                $motor->registration_year   = $request->registration_year;
                $motor->fuel_type           = $request->fuel;
                $motor->transmission        = $request->transmission;
                $motor->condition           = $request->condition;
                $motor->milage              = $request->mileage;
                $motor->save();

                if(isset($request->aircondition)){
                    $motorFeature           = new MotorFeatures();
                    $motorFeature->ads_id   = $ad->id;
                    $motorFeature->value    = $request->aircondition;
                    $motorFeature->save();
                }

                if(isset($request->gps)){
                    $motorFeature           = new MotorFeatures();
                    $motorFeature->ads_id   = $ad->id;
                    $motorFeature->value    = $request->gps;
                    $motorFeature->save();
                }

                if(isset($request->security)){
                    $motorFeature           = new MotorFeatures();
                    $motorFeature->ads_id   = $ad->id;
                    $motorFeature->value    = $request->security;
                    $motorFeature->save();
                }

                if(isset($request->tire)){
                    $motorFeature           = new MotorFeatures();
                    $motorFeature->ads_id   = $ad->id;
                    $motorFeature->value    = $request->tire;
                    $motorFeature->save();
                }
            }
            elseif($request->category == 2){

                if($request->parking){
                    $parking = 1;
                }
                else{
                    $parking = 0;
                }

                $property                   = new PropertyRendCustomeValues();
                $property->ads_id           = $ad->id;
                $property->size             = $request->size;
                $property->room             = $request->room;
                $property->furnished        = $request->furnished;
                $property->building_type    = $request->building;
                $property->parking          = $parking;
                $property->save();
            }
            elseif($request->category == 3){

                if($request->parking){
                    $parking = 1;
                }
                else{
                    $parking = 0;
                }

                $property                   = new PropertySaleCustomeValues();
                $property->ads_id           = $ad->id;
                $property->size             = $request->size;
                $property->room             = $request->room;
                $property->furnished        = $request->furnished;
                $property->building_type    = $request->building;
                $property->parking          = $parking;
                $property->save();
            }

            if($request->image){

                foreach($request->image as $row){

                    $image = $row['file'];

                    $image_parts = explode(";base64,", $image);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);

                    $ad_image = uniqid() . '.' .$image_type;

                    Storage::put('public/ads/'.$ad_image, $image_base64);

                    $ad_image = 'storage/ads/'.$ad_image;
    
                    $adImage            = new AdsImage();
                    $adImage->ads_id    = $ad->id;
                    $adImage->image     = $ad_image;
                    $adImage->save();
                }
            }
    
            if($request->fieldValue){

                foreach($request->fieldValue as $catRow){

                    $option = null;
                    $isFile = 0;

                    $field = Fields::where('id', $catRow['field_id'])
                    ->first();
                    
                    if($field->type == 'select' || $field->type == 'radio'){

                        $option = FieldOptions::where('field_id', $catRow['field_id'])
                        ->where('value', $catRow['value'])
                        ->first();

                    }
                    elseif($field->type == 'file'){
                        
                        $customFile = uniqid().'.'.$catRow['value']->getClientOriginalExtension();
                
                        $catRow['value']->storeAs('public/custom_file', $customFile);
        
                        $customFile = 'storage/custom_file/'.$customFile;

                        $isFile = 1;
                        $option = null;
                    }

                    if($option){
                        $option_id = $option->id;
                    }
                    else{
                        $option_id = 0;
                    }

                    if($field->type == 'file'){

                        $customValue            = new AdsCustomValue();
                        $customValue->ads_id    = $ad->id;
                        $customValue->field_id  = $catRow['field_id'];
                        $customValue->option_id = $option_id;
                        $customValue->value     = $customFile;
                        $customValue->file      = $isFile;
                        $customValue->save();
                    }
                    else{

                        $customValue            = new AdsCustomValue();
                        $customValue->ads_id    = $ad->id;
                        $customValue->field_id  = $catRow['field_id'];
                        $customValue->option_id = $option_id;
                        $customValue->value     = $catRow['value'];
                        $customValue->file      = $isFile;
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

            $notification           = new Notification();
            $notification->title    = 'New Ad Request';
            $notification->message  = 'New ad request';
            $notification->status   = 0;
            $notification->save();

            return response()->json([
                'status'    => 'success',
                'message'   => 'Ad request has been placed',
                'code'      => 200,
                'ad_id'     => $ad->id,
            ], 200);

        }
        catch (\Exception $e) {
            

            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
                'code'      => 400,
            ], 200);
        }

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
                'code'      => 400,
                'errors'    => $validate->errors(),
            ], 200);
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
                'code'      => 200,
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
                'code'      => 400,
                'errors'    => $validate->errors(),
            ], 200);
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
                'code'          => 200,
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

    public function getCategoryMotors(Request $request){

        try{

            // $rules = [
            //     'latitude'      => 'required|numeric',
            //     'longitude'     => 'required|numeric',
            // ];
    
            // $validate = Validator::make($request->all(), $rules);
    
            // if($validate->fails()){
    
            //     return response()->json([
            //         'status'    => 'error',
            //         'message'   => 'Invalid request',
            //         'code'      => 200,
            //         'errors'    => $validate->errors(),
            //     ], 200);
            // }

            $latitude = $request->latitude;
            $longitude = $request->longitude;
            
            $radius = 10; // Km
            $r = 6371000; // earth's mean radius 6371 for kilometer and 3956 for miles

            if($latitude != 0 && $longitude != 0){

                $motors = Category::where('id', 1)
                ->with(['Subcategory' => function($a) use($latitude, $longitude, $radius){
                    $a->where('parent_id', 0)
                    ->whereHas('Ads', function($a) use($latitude, $longitude, $radius){
                        $a->selectRaw('*, (6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    })
                    ->withCount(['Ads' => function($a) use($latitude, $longitude, $radius){
                        $a->selectRaw('*, (6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }]);
                }])
                ->first();
            }
            else{
                if($request->country && $request->city){
                    $motors = Category::where('id', 1)
                    ->with(['Subcategory' => function($a) use($request){
                        $a->where('parent_id', 0)
                        ->whereHas('Ads', function($a) use($request){
                            $a->where('country_id', $request->country)
                            ->where('city_id', $request->city);
                        })
                        ->withCount(['Ads' => function($a) use($request){
                            $a->where('country_id', $request->country);
                        }]);
                    }])
                    ->first();
                }
                elseif($request->country){

                    $motors = Category::where('id', 1)
                    ->with(['Subcategory' => function($a) use($request){
                        $a->where('parent_id', 0)
                        ->whereHas('Ads', function($a) use($request){
                            $a->where('country_id', $request->country);
                        })
                        ->withCount(['Ads' => function($a) use($request){
                            $a->where('country_id', $request->country);
                        }]);
                    }])
                    ->first();
                }
                elseif($request->city){
                    $motors = Category::where('id', 1)
                    ->with(['Subcategory' => function($a) use($request){
                        $a->where('parent_id', 0)
                        ->whereHas('Ads', function($a) use($request){
                            $a->where('city_id', $request->city);
                        })
                        ->withCount(['Ads' => function($a) use($request){
                            $a->where('country_id', $request->city);
                        }]);
                    }])
                    ->first();
                }
                else{
                    $motors = Category::where('id', 1)
                    ->with(['Subcategory' => function($a) use($request){
                        $a->where('parent_id', 0)
                        ->withCount('Ads');
                    }])
                    ->first();
                }
            }

            if($request->city){

                $city = City::where('id', $request->city)
                ->first();

                $ads = Ads::where('category_id', 1)
                // ->selectRaw('*, (6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius)
                // ->where(function($q) use($request, $city) {
                //     $q->orwhere('city_id', $request->city)
                //     ->orwhere('state_id', $city->state_id);   
                // })
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE)
                // ->where('featured_flag', 1)
                ->where('city_id', $request->city);

                if($latitude != 0 && $longitude != 0){

                    $ads->selectRaw('*, (6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius);
                }
                
                if(isset($request->country)){
                    $ads->where('country_id', $request->country);
                }

                $ads = tap($ads->paginate(20), function ($paginatedInstance){
                    return $paginatedInstance->getCollection()->map(function($a){
                            $a->state_name = $a->State ? $a->State->name : '';
                            $a->city_name = $a->City ? $a->City->name : ($a->State ? $a->State->name : '');
                            $a->make = $a->MotoreValue ? $a->MotoreValue->Make->name : '';
                            $a->model = $a->MotoreValue->Model->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->year = $a->MotoreValue->registration_year;
                            $a->milage = $a->MotoreValue->milage;
                            $a->image = $a->Image;
                            unset($a->MotoreValue, $a->State, $a->City, $a->Country);
                            return $a;
                        });
                    });
            }
            else{

                $ads = Ads::where('category_id', 1)
                // ->selectRaw('*, (6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius)
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE);
                // ->where('featured_flag', 1);

                if($latitude != 0 && $longitude != 0){

                    $ads->selectRaw('*, (6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius);
                }
                
                if(isset($request->country)){
                    $ads->where('country_id', $request->country);
                }

                $ads = tap($ads->paginate(20), function ($paginatedInstance){
                    return $paginatedInstance->getCollection()->map(function($a){
                            $a->state_name = $a->State->name;
                            $a->city_name = $a->City ? $a->City->name : $a->State->name;
                            if($a->MotoreValue){
                                $a->make = $a->MotoreValue->Make->name;
                                $a->model = $a->MotoreValue->Model->name;
                                $a->year = $a->MotoreValue->registration_year;
                                $a->milage = $a->MotoreValue->milage;

                                unset($a->MotoreValue);
                            }
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->image = $a->Image;
                            unset($a->State, $a->City, $a->Country);
                            return $a;
                        });
                    });

            }

            $testimonial = Testimonial::orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'status'    => 'success',
                'message'   => 'category list',
                'code'      => 200,
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

        try{
            
            $rules = [
                'category_id'   => 'required|numeric',
                // 'latitude'      => 'required|numeric',
                // 'longitude'     => 'required|numeric',

            ];

            $validate = Validator::make($request->all(), $rules);

            if($validate->fails()){

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Invalid request',
                    'code'      => 400,
                    'errors'    => $validate->errors(),
                ], 200);
            }

            $latitude = $request->latitude;
            $longitude = $request->longitude;
            
            $radius = 10; // Km
            $r = 6371000; // earth's mean radius 6371 for kilometer and 3956 for miles

            $property = Category::where('id', $request->category_id)
            ->with(['Subcategory' => function($a){
                $a->withCount('Ads');
            }])
            ->first();

            $subcategory = Subcategory::where('category_id', $request->category_id)
            ->where('status', Status::ACTIVE)
            ->where('delete_status', '!=', Status::DELETE)
            // ->with(['ads' => function($a) use($latitude, $longitude, $radius, $request, $city){
            //     $a->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
            //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            //     ->having('distance', '<=', $radius)
            //     ->where(function($b) use($request, $city){
            //         $b->orwhere('city_id', $request->city)
            //         ->orwhere('state_id', $city->state_id);
            //     });
            // }])
            // ->whereHas('ads', function($a) use($latitude, $longitude, $radius, $request, $city){
            //     $a->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
            //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            //     ->having('distance', '<=', $radius)
            //     ->where(function($b) use($request, $city){
            //         $b->orwhere('city_id', $request->city)
            //         ->orwhere('state_id', $city->state_id);
            //     });
            // })
            ->orderBy('sort_order');

            if($request->city){

                $city = City::where('id', $request->city)
                ->first();

                $subcategory->with(['ads' => function($a) use($request, $city){
                    $a->where(function($b) use($request, $city){
                        $b->orwhere('city_id', $request->city)
                        ->orwhere('state_id', $city->state_id);
                    });
                }]);
                // ->whereHas('ads', function($a) use($request, $city){
                //     $a->where(function($b) use($request, $city){
                //         $b->orwhere('city_id', $request->city)
                //         ->where(function($a) use($city){
                //             $a->where('city_id', 0)
                //             ->where('state_id', $city->state_id);
                //         });
                //     });
                // });
            }

            if($latitude != 0 && $longitude != 0){

                $subcategory->with(['ads' => function($a) use($latitude, $longitude, $radius){
                    $a->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius);
                }]);
                // ->whereHas('ads', function($a) use($latitude, $longitude, $radius){
                //     $a->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //     ->having('distance', '<=', $radius);
                // });
            }

                if(isset($request->country)){

                    $subcategory->with(['ads' => function($a) use($request){
                        $a->where('country_id', $request->country);
                    }]);
                    // ->whereHas('ads', function($a) use($request){
                    //     $a->where('country_id', $request->country);
                    // });

                }

                if($request->city){
                    $subcategory->with(['ads' => function($a) use($request){
                        $a->where('city_id', $request->city);
                    }]);
                    // ->whereHas('ads', function($a) use($request){
                    //     $a->where('city_id', $request->city);
                    // });
                }

                $subcategory = $subcategory->get()->map(function($a){

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

                        $b->currency = $b->Country->Currency ? $b->Country->Currency->currency_code : '';
                        $b->state_name = $b->State->name;
                        $b->city_name = $b->city_id ? $b->City->name : $b->State->name;
                        $b->Image;

                        unset($b->State, $b->City, $b->Country->Currency);
                        return $b;
                    });
                    return $a;
                });
            

            return response()->json([
                'status'    => 'success',
                'message'   => 'category list',
                'code'      => 200,
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

    public function getMake(){

        try{

            $make = MakeMst::where('status', Status::ACTIVE)
            ->orderBy('name')
            ->get();

            return response()->json([
                'status'    => 'success',
                'message'   => 'Make list',
                'code'      => 200,
                'make'      => $make,
            ], 200);
        }
        catch (\Exception $e) {
            
    
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getModel(Request $request){

        try{
            $model = ModelMst::where('make_id', $request->make_id)
            ->where('status', Status::ACTIVE)
            ->orderBy('name')
            ->get();

            return response()->json([
                'status'    => 'success',
                'message'   => 'Model list',
                'code'      => 200,
                'model'      => $model,
            ], 200);

        }
        catch (\Exception $e) {
            
    
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getVarieant(Request $request){

        try{

            $variant = VarientMst::where('model_id', $request->model_id)
            ->where('status', Status::ACTIVE)
            ->orderBy('name')
            ->get();

            return response()->json([
                'status'    => 'success',
                'message'   => 'Variant list',
                'code'      => 200,
                'variant'   => $variant,
            ], 200);

        }
        catch (\Exception $e) {
            
    
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function favouriteGet(Request $request){

        $rules = [
            'ads_id'    => 'required|numeric',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid request',
                'code'      => 400,
                'errors'    => $validate->errors(),
            ], 200);
        }

        try{
            
            $favourite = Favorite::where('ads_id', $request->ads_id)
            ->where('customer_id', Auth::user()->id)
            ->count();

            return response()->json([
                'status'    => 'success',
                'message'   => 'Favourite list',
                'code'      => 200,
                'favourite' => $favourite,
            ], 200);

        }
        catch (\Exception $e) {
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getPropertyFilter(Request $request){

        $rules = [
            'category_id'       => 'required|numeric',
            'subcategory_id'    => 'required|numeric',
            // 'latitude'          => 'required',
            // 'longitude'         => 'required',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid request',
                'code'      => 400,
                'errors'    => $validate->errors(),
            ], 200);
        }
        
        try{

            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $radius = 10; // Km

            $subcategory = Subcategory::where('id', $request->subcategory_id)
            ->first();
            
            if($request->city && $request->property_type && $request->price && $request->room){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('building_type', $request->property_type)
                        ->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);
                    
                    if($latitude != 0 && $longitude != 0){
                        
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('building_type', $request->property_type)
                        ->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->city && $request->property_type && $request->price){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('building_type', $request->property_type);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('building_type', $request->property_type);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->city && $request->property_type && $request->room){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('building_type', $request->property_type)
                        ->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->state_name = $a->State->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('building_type', $request->property_type)
                        ->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0 ){
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }
                    
                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->city && $request->property_type){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('building_type', $request->property_type);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('building_type', $request->property_type);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->city && $request->price && $request->room){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->city && $request->price){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->where('price', '<=', $request->price)
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->where('price', '<=', $request->price)
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius) ;
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->city && $request->room){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->city){
                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('city_id', $request->city)
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->property_type && $request->price && $request->room){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('building_type', $request->property_type)
                        ->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('building_type', $request->property_type)
                        ->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->property_type && $request->price){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('building_type', $request->property_type);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('building_type', $request->property_type);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->property_type && $request->room){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('building_type', $request->property_type)
                        ->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('building_type', $request->property_type)
                        ->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->property_type){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('building_type', $request->property_type);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('building_type', $request->property_type);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->price && $request->room){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('price', '<=', $request->price)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->price){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('price', '<=', $request->price)
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0) {
                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->where('price', '<=', $request->price)
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            elseif($request->room){

                if($request->category_id == 2){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->whereHas('PropertyRend', function($a) use($request){
                        $a->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
                elseif($request->category_id == 3){

                    $myAds = Ads::where('status', Status::ACTIVE)
                    // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius)
                    ->whereHas('PropertySale', function($a) use($request){
                        $a->where('room', '<=',$request->room);
                    })
                    ->where('delete_status', '!=', Status::DELETE)
                    ->where('category_id', $request->category_id)
                    ->where('subcategory_id', $request->subcategory_id);

                    if($latitude != 0 && $longitude != 0){

                        $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius);
                    }

                    if(isset($request->country)){
                    
                        $myAds->where('country_id', $request->country);
                    }

                    $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                        return $paginatedInstance->getCollection()->transform(function($a){

                            $a->image = array_filter([
                                $a->Image->map(function($q) use($a){
                                    $q->image;
                                    unset($q->ads_id, $q->img_flag);
                                    return $q;
                                }),
                            ]);

                            $a->subcategory_name = $a->Subcategory->name;
                                

                            $a->country_name = $a->Country->name;
                            $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                            $a->state_name = $a->State->name;
                            if($a->city_id != 0){
                                $a->city_name = $a->City->name;
                            }
                            else{
                                $a->city_name = $a->State->name;
                            }

                            if($a->category_id == 2){
                                $a->PropertyRend;
                            }
                            elseif($a->category_id == 3){
                                $a->PropertySale;
                            }

                            if($a->sellerinformation_id == 0){
                                $a->seller = 'admin';
                            }
                            else{
                                $a->seller = 'user';
                                $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                                $a->seller_phone =  $a->SellerInformation->phone;
                                $a->seller_email = $a->SellerInformation->email;

                                unset($a->SellerInformation);
                            }

                            $a->created_on = date('d-M-Y', strtotime($a->created_at));
                            $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                            unset($a->Country, $a->City, $a->State, $a->Subcategory);
                            return $a;
                        });
                    });
                }
            }
            else{
                
                $myAds = Ads::where('status', Status::ACTIVE)
                // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius)
                ->where('delete_status', '!=', Status::DELETE)
                ->where('category_id', $request->category_id)
                ->where('subcategory_id', $request->subcategory_id);

                if($latitude != 0 && $longitude != 0){

                    $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius);
                }

                if(isset($request->country)){
                    
                    $myAds->where('country_id', $request->country);
                }

                $myAds = tap($myAds->paginate(10), function($paginatedInstance){
                    return $paginatedInstance->getCollection()->transform(function($a){

                        $a->image = array_filter([
                            $a->Image->map(function($q) use($a){
                                $q->image;
                                unset($q->ads_id, $q->img_flag);
                                return $q;
                            }),
                        ]);

                        $a->subcategory_name = $a->Subcategory->name;
                            

                        $a->country_name = $a->Country->name;
                        $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                        $a->state_name = $a->State->name;
                        if($a->city_id != 0){
                            $a->city_name = $a->City->name;
                        }
                        else{
                            $a->city_name = $a->State->name;
                        }

                        if($a->category_id == 2){
                            $a->PropertyRend;
                        }
                        elseif($a->category_id == 3){
                            $a->PropertySale;
                        }

                        if($a->sellerinformation_id == 0){
                            $a->seller = 'admin';
                        }
                        else{
                            $a->seller = 'user';
                            $a->phone_hide = $a->SellerInformation->phone_hide_flag;
                            $a->seller_phone =  $a->SellerInformation->phone;
                            $a->seller_email = $a->SellerInformation->email;

                            unset($a->SellerInformation);
                        }

                        $a->created_on = date('d-M-Y', strtotime($a->created_at));
                        $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

                        unset($a->Country, $a->City, $a->State, $a->Subcategory);
                        return $a;
                    });
                });
            }

            return response()->json([
                'status'        => 'success',
                'message'       => 'Result list',
                'code'          => 200,
                'ads'           => $myAds,
                'subcategory'   => $subcategory,
            ], 200);


        }
        catch (\Exception $e) {
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function motorSearch(Request $request){

        try{
            
            // $rules = [
            //     // 'search_key'    => 'required',
            //     'latitude'      => 'required',
            //     'longitude'     => 'required',
            // ];
    
            // $validate = Validator::make($request->all(), $rules);
    
            // if($validate->fails()){
    
            //     return response()->json([
            //         'status'    => 'error',
            //         'message'   => 'Invalid request',
            //         'code'      => 400,
            //         'errors'    => $validate->errors(),
            //     ], 200);
            // }

            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $radius = 10; // Km

            
            $myAds = Ads::where('category_id', $request->category)
            // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
            //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            // ->having('distance', '<=', $radius)
            ->where(function($a) use($request){
                $a->orwhere('title', 'like', '%'.$request->search_key.'%')
                ->orwhere('canonical_name', 'like', '%'.$request->search_key.'%');
            })
            ->where('status', Status::ACTIVE)
            ->where('delete_status', '!=', Status::DELETE);
            
            if($latitude != 0 && $longitude != 0){

                $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius);
            }

            if(isset($request->country)){
                    
                $myAds->where('country_id', $request->country);
            }

            if($request->city){

                $city = City::where('id', $request->city)
                ->first();
                
                $myAds->where(function($a) use($request, $city){
                    $a->orwhere('city_id', $request->city)
                    ->where(function($a) use($city){
                        $a->where('city_id', 0)
                        ->where('state_id', $city->state_id);
                    });
                });
            }

            if($request->subcategory){

                $myAds->where('subcategory_id', $request->subcategory);
            }

            if($request->condition){
                
                $myAds->with(['MotoreValue' => function($q) use($request){
                    $q->where('condition', $request->condition);
                }])
                ->whereHas('MotoreValue', function($p) use($request){
                    $p->where('condition', $request->condition);
                });
            }

            if($request->transmission){

                $myAds->with(['MotoreValue' => function($q) use($request){
                    $q->where('transmission', $request->transmission);
                }])
                ->whereHas('MotoreValue', function($p) use($request){
                    $p->where('transmission', $request->transmission);
                });
            }

            if($request->priceFrom && $request->priceTo){
                $myAds->where('price', '>=', $request->priceFrom)
                ->where('price', '<=', $request->priceTo);
            }
            elseif($request->priceFrom){
                $myAds->where('price', '>=', $request->priceFrom);
            }
            elseif($request->priceTo){
                $myAds->where('price', '<=', $request->priceTo);;
            }

            if($request->yearFrom && $request->yearTo){
                $myAds->with(['MotoreValue' => function($q) use($request){
                    $q->where('registration_year', '>=', $request->yearFrom)
                    ->where('registration_year', '<=', $request->yearTo);
                }])
                ->whereHas('MotoreValue', function($p) use($request){
                    $p->where('registration_year', '>=', $request->yearFrom)
                    ->where('registration_year', '<=', $request->yearTo);
                });
            }
            elseif($request->yearFrom){
                $myAds->with(['MotoreValue' => function($q) use($request){
                    $q->where('registration_year', '>=', $request->yearFrom);
                }])
                ->whereHas('MotoreValue', function($p) use($request){
                    $p->where('registration_year', '>=', $request->yearFrom);
                });
            }
            elseif($request->yearTo){
                $myAds->with(['MotoreValue' => function($q) use($request){
                    $q->where('registration_year', '<=', $request->yearTo);
                }])
                ->whereHas('MotoreValue', function($p) use($request){
                    $p->where('registration_year', '<=', $request->yearTo);
                });
            }

            if($request->mileageFrom && $request->mileageTo){
                $myAds->with(['MotoreValue' => function($q) use($request){
                    $q->where('milage', '>=', $request->mileageFrom)
                    ->where('milage', '<=', $request->mileageTo);
                }])
                ->whereHas('MotoreValue', function($p) use($request){
                    $p->where('milage', '>=', $request->mileageFrom)
                    ->where('milage', '<=', $request->mileageTo);
                });
            }
            elseif($request->mileageFrom){
                $myAds->with(['MotoreValue' => function($q) use($request){
                    $q->where('milage', '>=', $request->mileageFrom);
                }])
                ->whereHas('MotoreValue', function($p) use($request){
                    $p->where('milage', '>=', $request->mileageFrom);
                });
            }
            elseif($request->mileageTo){
                $myAds->with(['MotoreValue' => function($q) use($request){
                    $q->where('milage', '<=', $request->mileageTo);
                }])
                ->whereHas('MotoreValue', function($p) use($request){
                    $p->where('milage', '<=', $request->mileageTo);
                });
            }
            
            if(isset($request->seller)){
                
                if($request->seller == 0){
                    $myAds->where('featured_flag', 0);
                }
                else{
                    $myAds->where('featured_flag', '!=', 0);
                }
            }

            $myAds = tap($myAds->paginate(10), function ($paginatedInstance){
                return $paginatedInstance->getCollection()->transform(function($a){

                    $a->image = array_filter([
                        $a->Image->map(function($q) use($a){
                            $q->image;
                            unset($q->ads_id, $q->img_flag);
                            return $q;
                        }),
                    ]);

                    if($a->category_id == 1){
                        $a->MotoreValue;
                        if($a->MotoreValue){
                            $a->make = $a->MotoreValue->Make ? $a->MotoreValue->Make->name : '';
                            $a->model = $a->MotoreValue->Model ? $a->MotoreValue->Model->name : '';
                            unset($a->MotoreValue->Make, $a->MotoreValue->Model);
                        }
                        // $a->MotorFeatures;
    
                        
                    }
                    elseif($a->category_id == 2){
                        $a->PropertyRend;
                    }
                    elseif($a->category_id ==3){
                        $a->PropertySale;
                    }

                    $a->country_name = $a->Country->name;
                    $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                    $a->state_name = $a->State->name;
                    $a->created_on = date('d-M-Y', strtotime($a->created_at));
                    $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

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

                    unset($a->status, $a->reject_reason_id, $a->delete_status, $a->Country, $a->State, $a->City);
                    return $a;
                });
            });

            return response()->json([
                'status'    => 'success',
                'message'   => 'Result for '. $request->search_key,
                'code'      => 200,
                'ads'       => $myAds,
            ]);
        }
        catch (\Exception $e) {
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function adsViewEntry(Request $request){

        try{

            $rules = [
                'ads_id'    => 'required|numeric',
            ];

            $validate = Validator::make($request->all(), $rules);
    
            if($validate->fails()){
    
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Invalid request',
                    'code'      => 400,
                    'errors'    => $validate->errors(),
                ], 200);
            }

            $ads = Ads::where('id', $request->ads_id)
            ->first();

            Ads::where('id', $request->ads_id)
            ->update([
                'view_count'    => $ads->view_count + 1,
            ]);

            return response()->json([
                'status'    => 'success',
                'message'   => 'View added',
                'code'      => 200,
            ]);
        }
        catch (\Exception $e) {
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function searchAutoComplete(Request $request){

        try{

            $rules = [
                'search_key'    => 'required',
                // 'latitude'      => 'required',
                // 'longitude'     => 'required',
                'country'       => 'required',
            ];

            $validate = Validator::make($request->all(), $rules);
    
            if($validate->fails()){
    
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Invalid request',
                    'code'      => 400,
                    'errors'    => $validate->errors(),
                ], 200);
            }

            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $radius = 10; // Km

            $ads = Ads::where(function($a) use($request){
                $a->orwhere('title', 'like', '%'. $request->search_key .'%')
                ->orwhere('title_arabic', 'like', '%'. $request->search_key .'%')
                ->orwhere('canonical_name', 'like', '%'. $request->search_key .'%');
            })
            ->where('country_id', $request->country)
            ->where('status', Status::ACTIVE)
            ->where('delete_status', Status::UNDELETE);
            // ->selectRaw('id, title as name, latitude, longitude, category_id, sellerinformation_id, subcategory_id, price, country_id, (6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
            //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            // ->having('distance', '<=', $radius);

            if($latitude != 0 && $longitude != 0){

                $ads->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius);
            }

            if(isset($request->country)){
                    
                $ads->where('country_id', $request->country);
            }

            if($request->category){
                $ads->where('category_id', $request->category);
            }
            if($request->city){
                $ads->where('city_id', $request->city);
            }
            if($request->subcategory){
                $ads->where('subcategory_id', $request->subcategory);
            }
            if($request->seller){
                $ads->where('featured_flag', $request->seller);
            }
            if($request->price_from){
                $ads->where('price', '>=', $request->price_from);
            }
            if($request->price_to){
                $ads->where('price', '<=', $request->price_to);
            }
            if($request->condition){
                $ads->where('category_id', 1)
                ->whereHas('MotoreValue', function($a) use($request){
                    $a->where('condition', $request->condition);
                });
            }
            if($request->transmission){
                $ads->where('category_id', 1)
                ->whereHas('MotoreValue', function($a) use($request){
                    $a->where('transmission', $request->transmission);
                });
            }
            if($request->mileage_from){
                $ads->where('category_id', 1)
                ->whereHas('MotoreValue', function($a) use($request){
                    $a->where('milage', '>=', $request->mileage_from);
                });
            }
            if($request->mileage_to){
                $ads->where('category_id', 1)
                ->whereHas('MotoreValue', function($a) use($request){
                    $a->where('milage', '<=', $request->mileage_to);
                });
            }

            $ads = $ads->get()->map(function($a){

                $a->images = count($a->Image) != 0 ? $a->Image[0]->image : '';
                $a->currency = $a->Currency->currency_code;

                unset($a->Image, $a->Currency);
                return $a;
            });

            return response()->json([
                'status'    => 'success',
                'message'   => 'Result for '. $request->search_key,
                'code'      => 200,
                'ads'       => $ads,
            ]);
        }
        catch (\Exception $e) {
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }
}
