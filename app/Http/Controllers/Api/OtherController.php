<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Http\Controllers\Controller;
use App\Mail\Enquiry;
use App\Models\Ads;
use App\Models\City;
use App\Models\Country;
use App\Models\Favorite;
use App\Models\FeaturedDealers;
use App\Models\SocialLink;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OtherController extends Controller
{
    public function favouriteView(){

        try{

            $favourite = tap(Favorite::where('customer_id', Auth::user()->id)
                ->paginate(12), function ($paginatedInstance){
                    return $paginatedInstance->getCollection()->transform(function($a){

                    $a->Ads;
                    $a->Ads->image = array_filter([
                        $a->Ads->Image->map(function($q) use($a){
                            $q->image;
                            unset($q->ads_id, $q->img_flag);
                            return $q;
                        }),
                    ]);

                    $a->Ads->country_name = $a->Ads->Country->name;
                    $a->currency = $a->Country->Currency ? $a->Country->Currency->currency_code : '';
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
            ->where('status', Status::ACTIVE)
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

                    unset($a->status, $a->reject_reason_id, $a->delete_status, $a->Country, $a->State, $a->City);
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
        
        $rules = [
            'search_key'    => 'required',
            'latitude'      => 'required',
            'longitude'     => 'required',
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

            if($request->city && $request->category){

                $myAds = tap(Ads::where('category_id', $request->category)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where(function($a) use($request){
                    $a->orwhere('title', 'like', '%'.$request->search_key.'%')
                    ->orwhere('canonical_name', 'like', '%'.$request->search_key.'%');
                })
                ->where('city_id', $request->city)
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(10), function ($paginatedInstance){
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

            }
            elseif($request->city){

                $myAds = tap(Ads::where('city_id', $request->city)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where(function($a) use($request){
                    $a->orwhere('title', 'like', '%'.$request->search_key.'%')
                    ->orwhere('canonical_name', 'like', '%'.$request->search_key.'%');
                })
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(10), function ($paginatedInstance){
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

            }
            elseif($request->category){

                $myAds = tap(Ads::where('category_id', $request->category)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where(function($a) use($request){
                    $a->orwhere('title', 'like', '%'.$request->search_key.'%')
                    ->orwhere('canonical_name', 'like', '%'.$request->search_key.'%');
                })
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(10), function ($paginatedInstance){
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

            }
            else{

                $myAds = tap(Ads::where('status', Status::ACTIVE)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where(function($a) use($request){
                    $a->orwhere('title', 'like', '%'.$request->search_key.'%')
                    ->orwhere('canonical_name', 'like', '%'.$request->search_key.'%');
                })
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(10), function ($paginatedInstance){
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

            }

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
            $rules = [
                'latitude'      => 'required',
                'longitude'     => 'required',
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

            if($request->city && $request->subcategory){

                $myAds = tap(Ads::where('status', Status::ACTIVE)
                ->where('category_id', 1)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where('delete_status', '!=', Status::DELETE)
                ->where('city_id', $request->city)
                ->where('subcategory_id', $request->subcategory)
                ->paginate(10), function ($paginatedInstance){
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
            }
            elseif($request->city){
                $myAds = tap(Ads::where('status', Status::ACTIVE)
                ->where('category_id', 1)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where('delete_status', '!=', Status::DELETE)
                ->where('city_id', $request->city)
                ->paginate(10), function ($paginatedInstance){
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
            }
            elseif($request->subcategory){

                $myAds = tap(Ads::where('status', Status::ACTIVE)
                ->where('category_id', 1)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where('delete_status', '!=', Status::DELETE)
                ->where('subcategory_id', $request->subcategory)
                ->paginate(10), function ($paginatedInstance){
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
            }
            else{

                $myAds = tap(Ads::where('status', Status::ACTIVE)
                ->where('category_id', 1)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(10), function ($paginatedInstance){
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
            }

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
            'canonical_name'    => 'required',
            'latitude'          => 'required',
            'longitude'         => 'required',
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
            
                $myAds = tap(Ads::selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where(function($a) use($request, $city){
                    $a->where('city_id', $request->city)
                    ->orwhere('state_id', $city->state_id);
                })
                ->where('status', Status::ACTIVE)
                ->whereHas('Category', function($a) use($request){
                    $a->where('canonical_name', $request->canonical_name);
                })
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(10), function ($paginatedInstance){
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

                $myAds = tap(Ads::selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where('status', Status::ACTIVE)
                ->whereHas('Category', function($a) use($request){
                    $a->where('canonical_name', $request->canonical_name);
                })
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(10), function ($paginatedInstance){
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
            'latitude'          => 'required',
            'longitude'         => 'required',
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
            
                $myAds = tap(Ads::where('subcategory_id', $request->subcategory_id)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where(function($a) use($request, $city){
                    $a->orwhere('city_id', $request->city)
                    ->orwhere('state_id', $city->state);
                })
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(10), function ($paginatedInstance){
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

                $myAds = tap(Ads::where('subcategory_id', $request->subcategory_id)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                        sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(10), function ($paginatedInstance){
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
}
