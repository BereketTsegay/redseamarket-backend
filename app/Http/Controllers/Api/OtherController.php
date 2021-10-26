<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Http\Controllers\Controller;
use App\Mail\ContactUs;
use App\Mail\Enquiry;
use App\Mail\Payment as MailPayment;
use App\Models\Ads;
use App\Models\Banner;
use App\Models\City;
use App\Models\ContactUs as ModelsContactUs;
use App\Models\Country;
use App\Models\CurrencyCode;
use App\Models\Favorite;
use App\Models\FeaturedDealers;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PrivacyPolicy;
use App\Models\SocialLink;
use App\Models\State;
use App\Models\Subcategory;
use App\Models\TermsConditions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class OtherController extends Controller
{
    public function favouriteView(){

        try{

            $favourite = tap(Favorite::where('customer_id', Auth::user()->id)
                ->whereHas('Ads')
                ->paginate(12), function ($paginatedInstance){
                    return $paginatedInstance->getCollection()->map(function($a){

                    $a->Ads;
                    $a->Ads->image = array_filter([
                        $a->Ads->Image->map(function($q) use($a){
                            $q->image;
                            unset($q->ads_id, $q->img_flag);
                            return $q;
                        }),
                    ]);

                    $a->Ads->country_name = $a->Ads->Country->name;
                    $a->currency = $a->Ads->Country->Currency ? $a->Ads->Country->Currency->currency_code : '';
                    $a->Ads->state_name = $a->Ads->State->name;
                    if($a->city_id != 0){
                        $a->city_name = $a->City->name;
                    }
                    else{
                        $a->city_name = $a->Ads->State->name;
                    }
                    $a->Ads->CustomValue->map(function($c){
                        
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

                    unset($a->Ads->status, $a->Ads->reject_reason_id, $a->Ads->delete_status, $a->Ads->Country, $a->Ads->State, $a->Ads->City);
                    return $a;
                });
            });

            return response()->json([
                'status'    => 'success',
                'message'   => 'My favourite ads',
                'code'      => 200,
                'favourite' => $favourite,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }

    }

    public function myAds(){

        try{

            $myAds = tap(Ads::where('customer_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->where('status', '!=', Status::REJECTED)
            ->where('delete_status', '!=', Status::DELETE)
            ->paginate(12), function ($paginatedInstance){
                return $paginatedInstance->getCollection()->transform(function($a){

                    $a->image = array_filter([
                        $a->Image->map(function($q) use($a){
                            $q->image;
                            unset($q->ads_id, $q->img_flag);
                            return $q;
                        }),
                    ]);

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

                    unset($a->reject_reason_id, $a->delete_status, $a->Country, $a->State, $a->City);
                    return $a;
                });
            });

            return response()->json([
                'status'    => 'success',
                'message'   => 'My ads',
                'code'      => 200,
                'ads'       => $myAds,
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function favouriteStoreOrRemove(Request $request){
        
        $rules = [
            'ads_id'    => 'required|numeric',
            'action'    => 'required',
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

            if($request->action == 'store'){

                $favourite              = new Favorite();
                $favourite->ads_id      = $request->ads_id;
                $favourite->customer_id = Auth::user()->id;
                $favourite->save();

                return response()->json([
                    'status'    => 'success',
                    'code'      => 200,
                    'message'   => 'Ad added to favourite',
                ], 200);
            }
            else{
                Favorite::where('ads_id', $request->ads_id)
                ->where('customer_id', Auth::user()->id)
                ->delete();

                return response()->json([
                    'status'    => 'success',
                    'message'   => 'Ad removed from favourite',
                ], 200);
            }

        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }

    }

    public function searchAds(Request $request){
        
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

            $myAds = Ads::where('status', Status::ACTIVE)
            // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
            //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            // ->having('distance', '<=', $radius)
            ->where('delete_status', '!=', Status::DELETE);

            if($latitude != 0 && $longitude != 0){

                $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius);
            }

            if(isset($request->country)){
                    
                $myAds->where('country_id', $request->country);
            }

            if(isset($request->city)){

                $myAds->where('city_id', $request->city);
            }

            if(isset($request->category)){
                $myAds->where('category_id', $request->category);
            }

            if(isset($request->subcategory)){
                $myAds->where('subcategory_id', $request->subcategory);
            }

            if($request->search_key){
                $myAds->where(function($a) use($request){
                    $a->orwhere('title', 'like', '%'.$request->search_key.'%')
                    ->orwhere('canonical_name', 'like', '%'.$request->search_key.'%');
                });
            }

            if(isset($request->seller)){

                if($request->seller == 0 || $request->seller == '0'){
                    $myAds->where('featured_flag', 0);
                }
                else{
                    $myAds->where('featured_flag', 1);
                }
            }

            if($request->priceFrom){
                $myAds->where('price', '>=', $request->priceFrom);
            }

            if($request->priceTo){
                $myAds->where('price', '<=', $request->priceTo);
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

                    $a->country_name = $a->Country->name;
                    $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                    $a->state_name = $a->State->name;
                    $a->created_on = date('d-M-Y', strtotime($a->created_at));
                    $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

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
                'message'   => 'Showing result for '. $request->search_key,
                'code'      => 200,
                'ads'       => $myAds,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getMototList(Request $request){

        try{

            // $rules = [
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

            $myAds = Ads::where('status', Status::ACTIVE)
            ->where('category_id', 1)
            // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
            //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            // ->having('distance', '<=', $radius)
            ->where('delete_status', '!=', Status::DELETE);

            if($latitude != 0 && $longitude != 0){

                $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius);
            }

            if($request->city){
                $myAds->where('city_id', $request->city);
            }

            if($request->subcategory){
                $myAds->where('subcategory_id', $request->subcategory);
            }

            if($request->country){
                $myAds->where('country_id', $request->country);
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

                    $a->country_name = $a->Country->name;
                    $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
                    $a->state_name = $a->State->name;
                    $a->created_on = date('d-M-Y', strtotime($a->created_at));
                    $a->updated_on = date('d-M-Y', strtotime($a->updated_at));

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
                'message'   => 'Showing result',
                'code'      => 200,
                'ads'       => $myAds,
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getCountry(){
        try{
            $country = Country::orderBy('name')
            ->get();

            return response()->json([
                'status'    => 'success',
                'message'   => 'Country List',
                'code'      => 200,
                'country'   => $country,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getState(Request $request){
        
        try{
            $state = State::where('country_id', $request->country)
            ->orderBy('name')
            ->get();

            return response()->json([
                'status'    => 'success',
                'message'   => 'State List',
                'code'      => 200,
                'state'     => $state,
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
        
    }

    public function getCity(Request $request){

        try{
            $city = City::where('state_id', $request->state)
            ->orderBy('name')
            ->get();

            return response()->json([
                'status'    => 'success',
                'message'   => 'City List',
                'code'      => 200,
                'city'      => $city,
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function adEnquiry(Request $request){

        $rules = [
            'id'        => 'required|numeric',
            'message'   => 'required',
            'name'      => 'required',
            'email'     => 'required|email',
            'phone'     => 'required|numeric',
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

            $ads = Ads::where('id', $request->id)
            ->first();

            $enquiry = [
                'title'         => $ads->title,
                'category'      => $ads->Category->name,
                'customer_name' => $request->name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'message'       => $request->message,
            ];

            Mail::to('anasmk0313@gmail.com')->send(new Enquiry($enquiry));

            return response()->json([
                'status'    => 'success',
                'code'      => 200,
                'message'   => 'Your enquiry has been plced.',
            ], 200);

        }
        catch (\Exception $e) {
            
    
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getCategoryAds(Request $request){

        $rules = [
            'category'    => 'required',
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

            if($request->city){

                $city = City::where('id', $request->city)
                ->first();
            
                $myAds = Ads::where(function($a) use($request, $city){
                    $a->orwhere('city_id', $request->city)
                    ->orwhere(function($a) use($city){
                        $a->where('city_id', 0)
                        ->where('state_id', $city->state_id);
                    });
                })
                // selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius)
                ->where('status', Status::ACTIVE)
                ->where('category_id', $request->category)
                ->where('delete_status', '!=', Status::DELETE);

                if($latitude != 0 && $longitude != 0){

                    $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius);
                }

                if(isset($request->country)){
                    
                    $myAds->where('country_id', $request->country);
                }

                if(isset($request->seller)){
                    $myAds->where('featured_flag', $request->seller);
                }
                if(isset($request->priceFrom)){
                    $myAds->where('price', '>=', $request->priceFrom);
                }
                if(isset($request->priceTo)){
                    $myAds->where('price', '<=', $request->priceTo);
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
                            $a->make = $a->MotoreValue->Make->name;
                            $a->model = $a->MotoreValue->Model->name;
                            $a->MotorFeatures;
        
                            unset($a->MotoreValue->Make, $a->MotoreValue->Model);
                        }
                        elseif($a->category_id == 2){
                            $a->PropertyRend;
                        }
                        elseif($a->category_id == 3){
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

            }
            else{

                $myAds = Ads::where('status', Status::ACTIVE)
                // selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius)
                ->where('category_id', $request->category)
                ->where('delete_status', '!=', Status::DELETE);

                if($latitude != 0 && $longitude != 0){

                    $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius);
                }

                if(isset($request->country)){
                    
                    $myAds->where('country_id', $request->country);
                }

                if(isset($request->seller)){
                    $myAds->where('featured_flag', $request->seller);
                }

                if(isset($request->priceFrom)){
                    $myAds->where('price', '>=', $request->priceFrom);
                }
                if(isset($request->priceTo)){
                    $myAds->where('price', '<=', $request->priceTo);
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
                            $a->make = $a->MotoreValue->Make->name;
                            $a->model = $a->MotoreValue->Model->name;
                            $a->MotorFeatures;
        
                            unset($a->MotoreValue->Make, $a->MotoreValue->Model);
                        }
                        elseif($a->category_id == 2){
                            $a->PropertyRend;
                        }
                        elseif($a->category_id == 3){
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
            }

                return response()->json([
                    'status'    => 'success',
                    'message'   => 'Showing result ',
                    'code'      => 200,
                    'ads'       => $myAds,
                ], 200);
            
        }
        catch (\Exception $e) {
            
    
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getSubcategoryAds(Request $request){

        $rules = [
            'subcategory_id'    => 'required',
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

        $subcategory = Subcategory::where('parent_id', $request->subcategory_id)
        ->select('id')
        ->get();

        $array = [$request->subcategory_id];

        foreach($subcategory as $row){

            $array[] = $row->id;
        }
        

        try{

            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $radius = 10; // Km

            if($request->city){

                $city = City::where('id', $request->city)
                ->first();
            
                $myAds = Ads::whereIn('subcategory_id', array_values($array))
                // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius)
                ->where(function($a) use($request, $city){
                    $a->orwhere('city_id', $request->city)
                    ->where(function($a) use($city){
                        $a->where('city_id', 0)
                        ->where('state_id', $city->state_id);
                    });
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
                            $a->make = $a->MotoreValue->Make->name;
                            $a->model = $a->MotoreValue->Model->name;
                            $a->MotorFeatures;
        
                            unset($a->MotoreValue->Make, $a->MotoreValue->Model);
                        }
                        elseif($a->category_id == 2){
                            $a->PropertyRend;
                        }
                        elseif($a->category_id == 3){
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
            }
            else{

                $myAds = Ads::whereIn('subcategory_id', array_values($array))
                // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius)
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
                            $a->make = $a->MotoreValue->Make->name;
                            $a->model = $a->MotoreValue->Model->name;
                            $a->MotorFeatures;
        
                            unset($a->MotoreValue->Make, $a->MotoreValue->Model);
                        }
                        elseif($a->category_id == 2){
                            $a->PropertyRend;
                        }
                        elseif($a->category_id == 3){
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
            }

            return response()->json([
                'status'    => 'success',
                'message'   => 'Showing result ',
                'code'      => 200,
                'ads'       => $myAds,
            ], 200);
            
        }
        catch (\Exception $e) {
            
    
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function socialLink(){
        
        try{
            $social = SocialLink::where('status', Status::ACTIVE)
            ->get()
            ->map(function($a){
                $a->social_icons = $a->Icon->name;
                unset($a->Icon);
                return $a;
            });

            return response()->json([
                'status'    => 'success',
                'message'   => 'Social Links',
                'code'      => 200,
                'social'    => $social,
            ], 200);
        }
        catch (\Exception $e) {
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function cityList(Request $request){

        try{

            $rules = [
                'country_id'    => 'required|numeric',
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

            $state = State::where('country_id', $request->country_id)
            ->get();

            $city = [];

            foreach($state as $row){

                $cities = City::where('state_id', $row->id)
                ->get();

                foreach($cities as $col){
                    $city[] = $col;
                }
            }

            $city = collect($city);

            $city = $city->sortBy('name');
            $city = array_values($city->toArray());

            return response()->json([
                'status'    => 'success',
                'message'   => 'City list',
                'code'      => 200,
                'city'      => $city,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function featuredDealer(){

        try{

            $featured = FeaturedDealers::where('status', Status::ACTIVE)
            ->get();

            return response()->json([
                'status'    => 'success',
                'message'   => 'Featured dealer list',
                'code'      => 200,
                'featured'  => $featured,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function recivePayment(Request $request){

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $customer = Customer::create([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => [
                'line1'         => $request->address,
                'postal_code'   => $request->zipcode,
                'city'          => $request->city,
                'state'         => $request->state,
                'country'       => $request->country,
            ],
        ]);

        $intent = PaymentIntent::create([
            'amount'                => $request->amount,
            'currency'              => $request->currency,
            'customer'              => $customer->id,
            'payment_method_types'  => ['card'],
            'description'           => 'Featured Ad payment',
            'shipping'              => [
                'name'  => $request->name,
                'phone' => $request->phone,
                'address'           => [
                    'line1'         => $request->address,
                    'city'          => $request->city,
                    'state'         => $request->state,
                    'country'       => $request->country,
                    'postal_code'   => $request->zipcode,
                ],
            ],
        ]);

        $client_secret = $intent->client_secret;

        
        $payment                = new Payment();
        $payment->payment_id    = $intent->id;
        $payment->amount        = $request->amount;
        $payment->ads_id        = 0;
        $payment->name          = $request->name;
        $payment->email         = $request->email;
        $payment->phone         = $request->phone;
        $payment->payment_type  = 0; // 0 for Payment through stripe
        $payment->status        = 'Payment started';
        $payment->save();

        $details = [
            'name'      => $request->name,
            'amount'    => $request->amount,
            'id'        => $intent->id,
            'date'      => $payment->created_at,
        ];

        Mail::to($request->email)->send(new MailPayment($details));

        return $client_secret;
    }

    public function getCurrency(Request $request){

        try{
            $currency = CurrencyCode::where('country_id', $request->country)
            ->first();

            return response()->json([
                'status'    => 'success',
                'code'      => '200',
                'currency'  => $currency,
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function paymentStatusUpdate(Request $request){

        $rules = [
            'payment_id'    => 'required',
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

        Payment::where('payment_id', $request->payment_id)
        ->update([
            'status'    => 'Success',
        ]);
    }

    public function getFeaturedAmount(Request $request){

        $rules = [
            'subcategory'    => 'required',
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

        $subcategory = Subcategory::where('id', $request->subcategory)
        ->first();

        return response()->json([
            'status'    => 'success',
            'code'      => '200',
            'subcategory'   => $subcategory,
        ]);
    }

    public function paymentDocument(Request $request){

        $rules = [
            'id'                => 'required|numeric',
            'transaction_id'    => 'required',
            'payment_slip'      => 'required',
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

        if($request->payment_slip){

            $document_part = explode(";base64,", $request->payment_slip);
            $image_type_aux = explode("image/", $document_part[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($document_part[1]);

            $document = uniqid() . '.' .$image_type;

            Storage::put('public/document/'.$document, $image_base64);

            $document = 'storage/document/'.$document;

        }

        Payment::where('ads_id', $request->id)
        ->update([
            'payment_id'    => $request->transaction_id,
            'document'      => $document,
        ]);

        return response()->json([
            'status'    => 'success',
            'code'      => '200',
            'message'   => 'Document has been uploaded',
        ], 200);
    }

    public function privacyPolicy(){

        $privacy = PrivacyPolicy::orderBy('created_at')
        ->get();

        return response()->json([
            'status'    => 'success',
            'code'      => '200',
            'privacy'   => $privacy,
        ], 200);
    }

    public function termsConditions(){

        $terms = TermsConditions::orderBy('created_at')
        ->get();

        return response()->json([
            'status'    => 'success',
            'code'      => '200',
            'terms'     => $terms,
        ], 200);
    }

    public function getHomeBanner(Request $request){

        try{

            $rules = [
                'country'   => 'required|numeric',
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

            $banner = Banner::where('country_id', $request->country)
            ->first();

            return response()->json([
                'status'    => 'success',
                'code'      => '200',
                'banner'    => $banner,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function contactEnquiry(Request $request){

        try{

            $rules = [
                'message'   => 'required',
                'name'      => 'required',
                'email'     => 'required|email',
                'phone'     => 'required|numeric',
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

            $contactus              = new ModelsContactUs();
            $contactus->name        = $request->name;
            $contactus->email       = $request->email;
            $contactus->phone       = $request->phone;
            $contactus->message     = $request->message;
            $contactus->save();

            $notification = new Notification();
            $notification->title    = 'Contact us enquiry';
            $notification->message  = 'New Contact Us Enquiry';
            $notification->status   = 0;
            $notification->save();

            $enquiry = [
                'customer_name' => $request->name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'message'       => $request->message,
            ];

            Mail::to('anasmk0313@gmail.com')->send(new ContactUs($enquiry));

            return response()->json([
                'status'    => 'success',
                'code'      => 200,
                'message'   => 'Your enquiry has been plced.',
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
