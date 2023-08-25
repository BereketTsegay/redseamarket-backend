<?php

namespace App\Http\Controllers\App;

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
use App\Models\AdsCountry;
use App\Models\Category;
use App\Models\JobDocument;
use App\Models\Enquiry as adEnq;
use AmrShawky\LaravelCurrency\Facade\Currency;
use Illuminate\Support\Str;
use App\Models\JobProfile;
use App\Models\JobProfileCompany;
use App\Models\SearchHistory;

class OtherController extends Controller
{
    public function favouriteView(){

        try{

            $favourite = tap(Favorite::where('customer_id', Auth::user()->id)
                ->whereHas('Ads')
                ->get(), function ($paginatedInstance){
                    return $paginatedInstance->map(function($a){

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
                    $favourite = Favorite::where('ads_id', $a->Ads->id)
                    ->where('customer_id', Auth::user()->id)
                    ->count();
                    $a->isFavourite=$favourite;
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

            $myAds = tap(Ads::with('Category')->with('Enquiries')->where('customer_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            // ->where('status', '!=', Status::REJECTED)
            ->where('delete_status', '!=', Status::DELETE)
            ->get(), function ($paginatedInstance){
                return $paginatedInstance->map(function($a){

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
                    $favourite = Favorite::where('ads_id', $a->id)
                    ->where('customer_id', Auth::user()->id)
                    ->count();
                    $a->isFavourite=$favourite;
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
        $search_key = $request->search_key ?? null;
        $country=$request->country ?? 1;
        try{

            if($search_key){
                $search=new SearchHistory();
                $search->user_id=Auth::user()->id;
                $search->search_key= $search_key;
                $search->save();

            }


            $countryAds=AdsCountry::where('country_id',$country)->get()->pluck('ads_id');

            $myAds = Ads::select('ads.*')
            ->join('ads_countries','ads_countries.ads_id','ads.id')
            ->where('ads_countries.country_id',$request->country)
            ->where('ads.status', Status::ACTIVE)
            ->whereIn('ads.id', $countryAds)
            ->where('ads.delete_status', '!=', Status::DELETE)
            ->where('ads.title', 'like', '%'.$search_key.'%')
            ->with(['Category','Subcategory']);

            if(isset($request->city)){

                $myAds->where('ads.city_id', $request->city);
            }

            if($request->priceFrom && $request->priceTo){
                $myAds->whereBetween('ads_countries.price', [$request->priceFrom,  $request->priceTo])->where('ads_countries.country_id',$request->country);

            }
            if($request->priceFrom){
                $myAds->where('ads_countries.price', '>=', $request->priceFrom)->where('ads_countries.country_id',$request->country);
            }

            if($request->priceTo){
                $myAds->where('ads_countries.price', '<=', $request->priceTo)->where('ads_countries.country_id',$request->country);
            }
            $myAds->orderBy('ads.created_at','DESC');
          
           
            $myAds = tap($myAds->get(), function ($paginatedInstance){
                return $paginatedInstance->transform(function($a){

                    $a->image = array_filter([
                        $a->Image->map(function($q) use($a){
                            $q->image;
                            unset($q->ads_id, $q->img_flag);
                            return $q;
                        }),
                    ]);
                    $favourite = Favorite::where('ads_id', $a->id)
                    ->where('customer_id', Auth::user()->id)
                    ->count();
                    $a->isFavourite=$favourite;
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

            $radius = 100; // Km

            $myAds = Ads::select('ads.*')
            ->join('ads_countries','ads_countries.ads_id','ads.id')
            ->where('ads_countries.country_id',$request->country)
            ->where('ads.status', Status::ACTIVE)
            ->where('ads.category_id', 1)
            
            // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
            //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            // ->having('distance', '<=', $radius)
            ->where('ads.delete_status', '!=', Status::DELETE);

            if($latitude != 0 && $longitude != 0){

                // $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius);
            }

            if($request->city){
                $myAds->where('ads.city_id', $request->city);
            }

            if($request->subcategory){
                $myAds->where('ads.subcategory_id', $request->subcategory);
            }

            if($request->country){
                $countryAds=AdsCountry::where('country_id',$request->country)->get()->pluck('ads_id');
                //return $countryAds;
                $myAds->whereIn('ads.id', $countryAds);
            }
            if(isset($request->area)){
                $myAds->where('ads.area', $request->area);
            }
            if(isset($request->subArea)){
                $myAds->where('ads.sub_area', $request->subArea);
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
                $myAds->whereBetween('ads_countries.price', [$request->priceFrom,  $request->priceTo])->where('ads_countries.country_id',$request->country);

            }
            elseif($request->priceFrom){
                $myAds->where('ads_countries.price', '>=', $request->priceFrom)->where('ads_countries.country_id',$request->country);
            }
            elseif($request->priceTo){
                $myAds->where('ads_countries.price', '<=', $request->priceTo)->where('ads_countries.country_id',$request->country);
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
                    $myAds->where('ads.featured_flag', 0);
                }
                else{
                    $myAds->where('ads.featured_flag', '!=', 0);
                }
            }
           
            $myAds->orderBy('ads.id','DESC');

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

    public function getJobList(Request $request){
        $search_key = $request->search_key ?? null;

        // try{

         //   $countryAds=AdsCountry::where('country_id',$request->country)->get()->pluck('ads_id');

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

            $radius = 100; // Km

            $myAds = Ads::select('ads.*')
            ->join('ads_countries','ads_countries.ads_id','ads.id')
            ->where('ads_countries.country_id',$request->country)
            ->where('ads.status', Status::ACTIVE)
            ->where('ads.category_id',$request->category)
            ->where('ads.title', 'like', '%'.$search_key.'%')
            ->where('ads.delete_status', '!=', Status::DELETE);
           // return $myAds->get();
            if($latitude != 0 && $longitude != 0){

                // $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius);
            }

            if($request->city){
                $myAds->where('ads.city_id', $request->city);
            }

            if($request->subcategory){
                $myAds->where('ads.subcategory_id', $request->subcategory);
            }

            if($request->country){
                $countryAds=AdsCountry::where('country_id',$request->country)->get()->pluck('ads_id');
                //return $countryAds;
                $myAds->whereIn('ads.id', $countryAds);
            }
            
            if(isset($request->area)){
                $myAds->where('ads.area', $request->area);
            }
            if(isset($request->subArea)){
                $myAds->where('ads.sub_area', $request->subArea);
            }

            if($request->priceFrom && $request->priceTo){
                $myAds->whereBetween('ads_countries.price', [$request->priceFrom,  $request->priceTo]);
                // $myAds->where('price', '>=', $request->priceFrom)
                // ->where('price', '<=', $request->priceTo);
            }
            elseif($request->priceFrom){
                $myAds->where('ads_countries.price', '>=', $request->priceFrom);
            }
            elseif($request->priceTo){
                $myAds->where('ads_countries.price', '<=', $request->priceTo);
            }

          
            $myAds->orderBy('ads.id','DESC');


           
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

        // }
        // catch(\Exception $e){
        //     return response()->json([
        //         'status'    => 'error',
        //         'message'   => 'Something went wrong',
        //     ], 301);
        // }
    }

    public function getCountry(){
        try{
            $country = Country::where('status',1)->orderBy('name')->get();

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

//        try{

            $enquiry = [
                'ad_id'         =>$request->id,
                'customer_name' => $request->name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'message'       => $request->message,
            ];
            if(\App\Models\Enquiry::create($enquiry)){
//            Mail::to('anasmk0313@gmail.com')->send(new Enquiry($enquiry));

            return response()->json([
                'status'    => 'success',
                'code'      => 200,
                'message'   => 'Your enquiry has been placed.',
            ], 200);
            }
//        }
//        catch (\Exception $e) {
//            
//    
//            return response()->json([
//                'status'    => 'error',
//                'message'   => 'Something went wrong',
//            ], 301);
//        }
    }

    public function getCategoryAds(Request $request){
        $rules = [
            'category'    => 'required',
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

            $radius = 100; // Km

                $myAds = Ads::select('ads.*')
                ->join('ads_countries','ads_countries.ads_id','ads.id')
                ->where('ads_countries.country_id',$request->country)
                ->where('ads.status', Status::ACTIVE)
                ->where('ads.category_id', $request->category)
                ->where('ads.delete_status', '!=', Status::DELETE);

                if(isset($request->country)){
                    $countryAds=AdsCountry::where('country_id',$request->country)->get()->pluck('ads_id');

                    $myAds->whereIn('ads.id', $countryAds);
                }

                if(isset($request->state_id)){
                   
                    $myAds->where('ads.state_id', $request->state_id);
                }
                if(isset($request->city)){
                   
                    $myAds->where('ads.city_id', $request->city);
                }
                if(isset($request->area)){
                   
                    $myAds->where('ads.area', $request->area);
                }
                if(isset($request->subArea)){
                    $myAds->where('ads.sub_area', $request->subArea);
                }
                if($request->priceFrom && $request->priceTo){
                    $myAds->whereBetween('ads_countries.price', [$request->priceFrom,  $request->priceTo]);
                    
                }

                if($request->sort){
                    if($request->sort=='high-to-low'){
                        //return 2;
                        $myAds->orderBy('ads.price','DESC');
                    }
                    if($request->sort=='low-to-high'){
                        //return 1;
                        $myAds->orderBy('ads.price','ASC');
                    }
                }else{
                    $myAds->orderBy('ads.id','DESC');
                }
                $myAds = $myAds->get();
                     $myAds->map(function($a){

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
                        $favourite = Favorite::where('ads_id', $a->id)
                        ->where('customer_id', Auth::user()->id)
                        ->count();
                        $a->isFavourite=$favourite;
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

            $radius = 100; // Km

            if($request->city){

                $city = City::where('id', $request->city)
                ->first();
            
                $myAds = Ads::select('ads.*')
                ->join('ads_countries','ads_countries.ads_id','ads.id')
                ->where('ads_countries.country_id',$request->country)
                ->whereIn('ads.subcategory_id', array_values($array))
                // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius)
                ->where(function($a) use($request, $city){
                    $a->orwhere('ads.city_id', $request->city)
                    ->where(function($a) use($city){
                        $a->where('ads.city_id', 0)
                        ->where('ads.state_id', $city->state_id);
                    });
                })
                ->where('ads.status', Status::ACTIVE)
                ->where('ads.delete_status', '!=', Status::DELETE);

                if($latitude != 0 && $longitude != 0){

                    // $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius);
                }

                if(isset($request->country)){
                    $countryAds=AdsCountry::where('country_id',$request->country)->get()->pluck('ads_id');

                    $myAds->whereIn('ads.id', $request->country);
                }
               
                $myAds->orderBy('ads.id','DESC');
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

                $myAds = Ads::select('ads.*')
                ->join('ads_countries','ads_countries.ads_id','ads.id')
                ->where('ads_countries.country_id',$request->country)
                ->whereIn('ads.subcategory_id', array_values($array))
                // ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                // ->having('distance', '<=', $radius)
                ->where('ads.status', Status::ACTIVE)
                ->where('ads.delete_status', '!=', Status::DELETE);

                if($latitude != 0 && $longitude != 0){

                    // $myAds->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    //     sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    // ->having('distance', '<=', $radius);
                }

                if(isset($request->country)){
                    $countryAds=AdsCountry::where('country_id',$request->country)->get()->pluck('ads_id');

                    $myAds->whereIn('ads.id', $countryAds);
                }
               
                $myAds->orderBy('ads.id','DESC');
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
       // 'currency'              => $request->currency,

        $intent = PaymentIntent::create([
            'amount'                => round($request->amount*100),
            'currency'              => 'usd',
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

           $usdval= Currency::convert()
                ->from($currency->currency_code)
                ->to('USD')
                ->round(5)
                ->get();

            return response()->json([
                'status'    => 'success',
                'code'      => '200',
                'currency'  => $currency,
                'usdval'    => $usdval,
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
        if($subcategory){
            return response()->json([
                'status'    => 'success',
                'code'      => '200',
                'subcategory'   => $subcategory,
            ]);
        }

        else{

            $category = Category::where('id', $request->category)
            ->first();
            return response()->json([
                'status'    => 'success',
                'code'      => '200',
                'subcategory'   => $category,
            ]);
        }

       
    }

    public function paymentDocument(Request $request){
       //  dd($request->payment_slip);

        $rules = [
            'id'                => 'required|numeric',
            'transaction_id'    => 'required|unique:payments,payment_id',
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
       $documents=[];

        if(count($request->payment_slip)!=0){
            for($i=0;$i<count($request->payment_slip);$i++){

            $document = uniqid().'.'.$request->payment_slip[$i]->getClientOriginalExtension();

            $request->payment_slip[$i]->storeAs('public/document/', $document);

            $document = 'storage/document/'.$document;

            array_push($documents,$document);

        }
        }

        $parentpay=Payment::where('ads_id', $request->id)->where('parent',0)->first();

        $ad=Ads::find($request->id);
        $ad->status=0;
        $ad->update();
        $payment=new Payment();
        $payment->ads_id=$request->id;
        $payment->payment_id=$request->transaction_id;
        $payment->amount=$parentpay->amount;
        $payment->name=$parentpay->name;
        $payment->email=$parentpay->email;
        $payment->phone=$parentpay->phone;
        $payment->payment_type=$parentpay->payment_type;
        $payment->status=$parentpay->status;
        $payment->document=json_encode($documents);
        $payment->parent=$parentpay->id;
        $payment->save();
       
        // ->update([
        //     'payment_id'    => $request->transaction_id,
        //     'document'      => $document,
        // ]);

        return response()->json([
            'status'    => 'success',
            'code'      => '200',
            'message'   => 'Document has been uploaded',
        ], 200);
    }

    public function cvDocument(Request $request){
      //  dd($request->all());
        $rules = [
            'id'                => 'required|numeric',
            'cv_doc'      => 'required',
        ];
        $user=Auth::user();
        $validate = Validator::make($request->all(), $rules);
    
        if($validate->fails()){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid request',
                'code'      => 400,
                'errors'    => $validate->errors(),
            ], 200);
        }

        if($request->cv_doc){

            $document_part = explode(";base64,", $request->cv_doc);
            $doc_type_aux = explode("application/", $document_part[0]);
            $doc_type = $doc_type_aux[1];
            $doc_base64 = base64_decode($document_part[1]);

            $document = uniqid() . '.' .$doc_type;

            Storage::put('public/document/'.$document, $doc_base64);

            $document = 'storage/document/'.$document;

        }

       $doc= new JobDocument();
       $doc->ads_id = $request->id;
       $doc->document = $document;
       $doc->user_id = $user->id;
       $doc->save();

        return response()->json([
            'status'    => 'success',
            'code'      => '200',
            'message'   => 'Document has been uploaded',
        ], 200);
    }

    public function saveJobrequest(Request $request){
      

        $user=Auth::user();

        $file = uniqid().'.'.$request->cv_file->getClientOriginalExtension();
        $request->cv_file->storeAs('public/document/', $file);
        $file = 'storage/document/'.$file;

        $doc= new JobDocument();
        $doc->ads_id = $request->ad_id;
        $doc->document = $file;
        $doc->user_id = $user->id;
        $doc->total_experience = $request->total_experience;
        $doc->current_ctc = $request->current_ctc;
        $doc->expect_ctc = $request->expect_ctc;
        $doc->notice_period = $request->notice_period;
        $doc->relevent_field = $request->relevent_field;
        $doc->current_company = $request->current_company;
        $doc->save();

        return response()->json([
            'status'    => 'success',
            'code'      => '200',
        ], 200);

    }

    public function checkDocument(Request $request){

        $user=Auth::user();
        $document=JobDocument::where('ads_id',$request->ads_id)->where('user_id',$user->id)->first();
        if($document){
            return response()->json([
                'status'    => 1,
                'code'      => '200',
            ], 200);
        }
        else{
            return response()->json([
                'status'    => 0,
                'code'      => '200',
            ], 200);
        }

        
    }


    public function privacyPolicy(){

        $privacy = PrivacyPolicy::first();

        return response()->json([
            'status'    => 'success',
            'code'      => '200',
            'privacy'   => $privacy->policy,
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
                'message'   => 'Your enquiry has been successfully placed.',
            ], 200);

        }
        catch (\Exception $e) {
            
    
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function allCategories(){

        $data = Category::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->with('Subcategory')->withCount('Subcategory')->orderBy('subcategory_count','DESC')->get();

        return response()->json([
            'status'    => 'success',
            'data'   => $data,
        ], 200);
    }

    public function Transactions(Request $request){

       $user_id=Auth::user()->id;
       $ads=Ads::where('customer_id',$user_id)->get()->pluck('id');
       $payments=Payment::whereIn('ads_id',$ads)->where('parent','!=',0)->with('Ad')->orderBy('created_at','desc')->get()
       ->map(function($payment){
           $payment->document=json_decode($payment->document);
           return $payment;
       });

       return response()->json([
        'status'    => 'success',
        'data'   => $payments,
    ], 200);
    }

    public function adEnquirylist(Request $request){

       // $user_id=Auth::user()->id;
       $ads_enquiry=adEnq::where('ad_id',$request->ad_id)->get();

       return response()->json([
        'status'    => 'success',
        'data'   => $ads_enquiry,
    ], 200);
    }

    public function jobProfile(Request $request){
       $user_id=Auth::user()->id;
       $data= JobProfile::with('Company')->where('user_id',$user_id)->latest()->first();
       return response()->json([
        'status'    => 'success',
        'data'   => $data,
    ], 200);

    }

    public function jobProfileSave(Request $request){

     //   return $request;

        try{
            $rules = [
                'title'   => 'required',
                'education'      => 'required',
                'certificate'     => 'required',
                'language'     => 'required',
                'skils'     => 'required',
                'cv_file'     => 'required',
                'overview'     => 'required',
                'country_id'     => 'required',
                'state_id'     => 'required',
                'city_id'     => 'required',
                'company'     => 'required',

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

            $data=new JobProfile();
            $data->user_id=Auth::user()->id;
            $data->title=$request->title;
            $data->work_experience=$request->work_experience;
            $data->education=$request->education;
            $data->certificate=$request->certificate;
            $data->language=$request->language;
            $data->skils=$request->skils;
            if($request->cv_file){
                $file = uniqid().'.'.$request->cv_file->getClientOriginalExtension();
                    
                $request->cv_file->storeAs('public/cv', $file);

                $file = 'storage/cv/'.$file;
                $data->cv_file=$file;
            }
            $data->overview=$request->overview;
            $data->country_id=$request->country_id;
            $data->state_id=$request->state_id;
            $data->city_id=$request->city_id;
            $data->save();

            foreach($request->company as $row){
            $company=new JobProfileCompany();
            $company->job_profile_id=$data->id;
            $company->from_date=$row['from_date'];
            $company->to_date=$row['to_date'];
            $company->company=$row['company'];
            $company->save();
            }

            return response()->json([
                'status'    => 'success',
            ], 200);

       }
        catch (\Exception $e) {
            
    
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function jobProfileUpdate(Request $request){

        try{
            $rules = [
                'title'   => 'required',
                'education'      => 'required',
                'certificate'     => 'required',
                'language'     => 'required',
                'skils'     => 'required',
                'overview'     => 'required',
                'country_id'     => 'required',
                'state_id'     => 'required',
                'city_id'     => 'required',
                'company'     => 'required',

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

            $data=JobProfile::find($request->jobprofile_id);
            $data->user_id=Auth::user()->id;
            $data->title=$request->title;
            $data->work_experience=$request->work_experience;
            $data->education=$request->education;
            $data->certificate=$request->certificate;
            $data->language=$request->language;
            $data->skils=$request->skils;
            if($request->cv_file){
                $file = uniqid().'.'.$request->cv_file->getClientOriginalExtension();
                    
                $request->cv_file->storeAs('public/cv', $file);

                $file = 'storage/cv/'.$file;
                $data->cv_file=$file;
            }
            $data->overview=$request->overview;
            $data->country_id=$request->country_id;
            $data->state_id=$request->state_id;
            $data->city_id=$request->city_id;
            $data->update();
            JobProfileCompany::where('job_profile_id',$data->id)->delete();
            foreach($request->company as $row){
            $company=new JobProfileCompany();
            $company->job_profile_id=$data->id;
            $company->from_date=$row['from_date'];
            $company->to_date=$row['to_date'];
            $company->company=$row['company'];
            $company->save();
            }

            return response()->json([
                'status'    => 'success',
            ], 200);

       }
        catch (\Exception $e) {
            
    
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
        
    }

    public function jobProfileList(){

        $user_id=Auth::user()->id;
        $data=JobProfile::with('User')->where('user_id','<>',$user_id)->get();
        return response()->json([
            'status'    => 'success',
            'data'      => $data,
        ], 200);
    }

    public function jobProfileDetails(Request $request){
        $data=JobProfile::with('User')->where('id',$request->profile_id)->first();
        return response()->json([
            'status'    => 'success',
            'data'      => $data,
        ], 200);
    }

    public function searchAlert(Request $request){
        $country=$request->country ?? 1;

        $data=SearchHistory::where('user_id',Auth::user()->id)->orderBy('id','DESC')->get()->take(5);
        $data->map(function($a,$country){

            $countryAds=AdsCountry::where('country_id',$country)->get()->pluck('ads_id');

            // $myAds = Ads::select('ads.*')
            // ->join('ads_countries','ads_countries.ads_id','ads.id')
            // ->where('ads.status', Status::ACTIVE)
            // ->whereIn('ads.id', $countryAds)->get()->count();
            $a->ads_count=0;
        });
        return response()->json([
            'status'    => 'success',
            'data'      => $data,
        ], 200);
    }

}
