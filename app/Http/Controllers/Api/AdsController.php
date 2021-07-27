<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\AdsImage;
use App\Models\CategoryField;
use App\Models\City;
use App\Models\Country;
use App\Models\MakeMst;
use App\Models\ModelMst;
use App\Models\SellerInformation;
use App\Models\State;
use App\Models\VarientMst;
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
