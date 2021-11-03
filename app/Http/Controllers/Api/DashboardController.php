<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\City;
use App\Models\Subcategory;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;

class DashboardController extends Controller
{

    public function dashboard(Request $request){

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

        try{
                
                if(Auth::user()){
                    $user = true;
                    $userName = Auth::user()->name;
                }
                {
                    $user = false;
                    $userName = '';
                }
            

            $latitude = $request->latitude;
            $longitude = $request->longitude;
            
            $radius = 10; // Km
            $r = 6371000; // earth's mean radius 6371 for kilometer and 3956 for miles
            

            if(isset($request->city)){

                $city = City::where('id', $request->city)
                ->first();
                
                $category = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order')
                    ->take(4);
                }])
                // ->with(['Ads' => function($b) use($latitude, $longitude, $radius, $request, $city){
                //     $b->where('status', Status::ACTIVE)
                //     ->where(function($q) use($request, $city){
                //         $q->orwhere('city_id', $request->city)
                //         ->orwhere('state_id', $city->state_id);
                //     })
                //     ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                //         // ->with(['CustomValue' => function($c){
                //         //     $c->with(['Field' => function($d){
                //         //         $d->where('status', Status::ACTIVE)
                //         //         ->where('delete_status', '!=', Status::DELETE);
                //         //     }]);
                //         // }]);
                // }])
                // ->whereHas('Ads',function($b) use($latitude, $longitude, $radius, $request, $city){
                //     $b->where('status', Status::ACTIVE)
                //     ->where(function($q) use($request, $city){
                //         $q->orwhere('city_id', $request->city)
                //         ->orwhere('state_id', $city->state_id);
                //     })
                //     ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                // })
                ->take(5);

                if(isset($request->country)){

                    $category->with(['Ads' => function($b) use($latitude, $longitude, $radius, $request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    }])
                    ->whereHas('Ads',function($b) use($latitude, $longitude, $radius, $request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    });
                }

                $category = $category->get()->map(function($a){

                    $a->Subcategory->map(function($c){

                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    $a->Ads->map(function($b){

                        $b->country = $b->Country->name;
                        $b->currency = $b->Country->Currency ? $b->Country->Currency->currency_code : '';
                        $b->state = $b->State->name;
                        $b->city = $b->City ? $b->City->name : $b->State->name ;
                        $b->CustomValue->map(function($c){
                            
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

                        $b->image = array_filter([
                            $b->Image->map(function($q) use($b){
                                $q->image;
                                unset($q->ads_id, $q->img_flag);
                                return $q;
                            }),
                        ]);
                            
                        unset($b->Image->ads_id, $b->Image->img_flag);
                        unset($b->Country, $b->State, $b->City, $b->category_id, $b->subcategory_id, $b->country_id, $b->state_id, $b->city_id, $b->sellerinformation_id, $b->customer_id, $b->payment_id, $b->delete_status, $b->status, $b->reject_reason_id);
                        return $b;
                    });
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });

                $categoryDefault = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order');
                }])
                // ->whereHas('Ads',function($b) use($latitude, $longitude, $radius, $request, $city){
                //     $b->where('status', Status::ACTIVE)
                //     ->where(function($q) use($request, $city){
                //         $q->orwhere('city_id', $request->city)
                //         ->orwhere('state_id', $city->state_id);
                //     })
                //     ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                // })
                ->take(5);

                if(isset($request->country)){

                    $categoryDefault->with(['Ads' => function($b) use($request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    }])
                    ->whereHas('Ads', function($b) use($request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    });
                }

                $categoryDefault = $categoryDefault->get()->map(function($a){

                    $a->Subcategory->map(function($c){
                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });

                $otherCategory = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', '!=', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order')
                    ->take(4);
                }]);

                if(isset($request->country)){

                    $otherCategory->with(['Ads' => function($b) use($request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    }])
                    ->whereHas('Ads', function($b) use($request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    });
                }

                $otherCategory = $otherCategory->get()->map(function($a){

                    $a->Subcategory->map(function($c){
                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });

            }
            else{
                

                $category = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order')
                    ->take(4);
                }])
                // ->with(['Ads' => function($b) use($latitude, $longitude, $radius){
                //     $b->where('status', Status::ACTIVE)
                //     ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                //         // ->with(['CustomValue' => function($c){
                //         //     $c->with(['Field' => function($d){
                //         //         $d->where('status', Status::ACTIVE)
                //         //         ->where('delete_status', '!=', Status::DELETE);
                //         //     }]);
                //         // }]);
                // }])
                // ->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                //     $b->where('status', Status::ACTIVE)
                //     ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                // })
                ->take(5);
                

                if($latitude != 0 && $longitude != 0){

                    $category->with(['Ads' => function($b) use($latitude, $longitude, $radius){
                            $b->where('status', Status::ACTIVE)
                            ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                                sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                                ->having('distance', '<=', $radius);
                        }])
                        ->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                            $b->where('status', Status::ACTIVE)
                            ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                                sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                                ->having('distance', '<=', $radius);
                    });
                }

                if(isset($request->country)){
                    
                    $category->with(['Ads' => function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    }])
                    ->whereHas('Ads', function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    });
                }

                if(!isset($request->country) && ($latitude == 0 && $longitude == 0)){

                    $category->with(['Ads' => function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', 229);
                    }])
                    ->whereHas('Ads', function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', 229);
                    });
                }

                $category = $category->get()
                ->map(function($a){

                    $a->Subcategory->map(function($c){

                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    $a->Ads->map(function($b){

                        $b->country = $b->Country->name;
                        $b->currency = $b->Country->Currency ? $b->Country->Currency->currency_code : '';
                        $b->state = $b->State->name;
                        $b->city = $b->City ? $b->City->name : $b->State->name ;
                        $b->CustomValue->map(function($c){
                            
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

                        $b->image = array_filter([
                            $b->Image->map(function($q) use($b){
                                $q->image;
                                unset($q->ads_id, $q->img_flag);
                                return $q;
                            }),
                        ]);
                            
                        unset($b->Image->ads_id, $b->Image->img_flag);
                        unset($b->Country, $b->State, $b->City, $b->category_id, $b->subcategory_id, $b->country_id, $b->state_id, $b->city_id, $b->sellerinformation_id, $b->customer_id, $b->payment_id, $b->delete_status, $b->status, $b->reject_reason_id);
                        return $b;
                    });
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });

                $categoryDefault = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order');
                }])
                // ->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                //     $b->where('status', Status::ACTIVE)
                //     ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                // })
                ->take(5);

                if($latitude != 0 && $longitude != 0){

                    $categoryDefault->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                        $b->where('status', Status::ACTIVE)
                        ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                            ->having('distance', '<=', $radius);
                    });
                }

                if(isset($request->country)){
                   
                    $categoryDefault->with(['Ads' => function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    }])
                    ->whereHas('Ads',function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    });
                }

                if(!isset($request->country) && ($latitude == 0 && $longitude == 0)){

                    $categoryDefault->with(['Ads' => function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', 229);
                    }])
                    ->whereHas('Ads', function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', 229);
                    });
                }

                $categoryDefault = $categoryDefault->get()->map(function($a){

                    $a->Subcategory->map(function($c){
                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });
                
                $otherCategory = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', '!=', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order');
                }]);

                if($latitude != 0 && $longitude != 0){

                    $otherCategory->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                        $b->where('status', Status::ACTIVE)
                        ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                            ->having('distance', '<=', $radius);
                    });
                }

                if(isset($request->country)){
                   
                    $otherCategory->with(['Ads' => function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    }])
                    ->whereHas('Ads',function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    });
                }

                $otherCategory = $otherCategory->get()->map(function($a){

                    $a->Subcategory->map(function($c){
                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });
            }


            return response()->json([
                'status'    => 'success',
                'code'      => 200,
                'data'      => [
                    'loged_user_status' => $user,
                    'user_name'         => $userName,
                    'category_default'  => $categoryDefault,
                    'categories'        => $category,
                    'otherCategory'     => $otherCategory,
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

    public function LogedDashboard(Request $request){

        // $rules = [
        //     'latitude'      => 'required|numeric',
        //     'longitude'     => 'required|numeric',
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

        try{

            if(Auth::check()){
                
                $user = true;
                
                $userName = Auth::user()->name;
                
            }
            else{
                $user = false;
                $userName = '';
            }

            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $radius = 10; // Km
            $r = 6371000; // earth's mean radius 6371 for kilometer and 3956 for miles
            //id, user_id, name, address_line, city, longitude, latitude, mobile, email, profile_image

            if(isset($request->city)){

                $city = City::where('id', $request->city)
                ->first();
                
                $category = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order')
                    ->take(4);
                }])
                // ->with(['Ads' => function($b) use($latitude, $longitude, $radius, $request, $city){
                //     $b->where('status', Status::ACTIVE)
                //     ->where(function($q) use($request, $city){
                //         $q->orwhere('city_id', $request->city)
                //         ->orwhere('state_id', $city->state_id);
                //     })
                //     ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                //         // ->with(['CustomValue' => function($c){
                //         //     $c->with(['Field' => function($d){
                //         //         $d->where('status', Status::ACTIVE)
                //         //         ->where('delete_status', '!=', Status::DELETE);
                //         //     }]);
                //         // }]);
                // }])
                // ->whereHas('Ads',function($b) use($latitude, $longitude, $radius, $request, $city){
                //     $b->where('status', Status::ACTIVE)
                //     ->where(function($q) use($request, $city){
                //         $q->orwhere('city_id', $request->city)
                //         ->orwhere('state_id', $city->state_id);
                //     })
                //     ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                // })
                ->take(5);

                if(isset($request->country)){

                    $category->with(['Ads' => function($b) use($latitude, $longitude, $radius, $request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    }])
                    ->whereHas('Ads',function($b) use($latitude, $longitude, $radius, $request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    });
                }

                $category = $category->get()->map(function($a){

                    $a->Subcategory->map(function($c){

                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    $a->Ads->map(function($b){

                        $b->country = $b->Country->name;
                        $b->currency = $b->Country->Currency ? $b->Country->Currency->currency_code : '';
                        $b->state = $b->State->name;
                        $b->city = $b->City ? $b->City->name : $b->State->name ;
                        $b->CustomValue->map(function($c){
                            
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

                        $b->image = array_filter([
                            $b->Image->map(function($q) use($b){
                                $q->image;
                                unset($q->ads_id, $q->img_flag);
                                return $q;
                            }),
                        ]);
                            
                        unset($b->Image->ads_id, $b->Image->img_flag);
                        unset($b->Country, $b->State, $b->City, $b->category_id, $b->subcategory_id, $b->country_id, $b->state_id, $b->city_id, $b->sellerinformation_id, $b->customer_id, $b->payment_id, $b->delete_status, $b->status, $b->reject_reason_id);
                        return $b;
                    });
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });

                $categoryDefault = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order');
                }])
                // ->whereHas('Ads',function($b) use($latitude, $longitude, $radius, $request, $city){
                //     $b->where('status', Status::ACTIVE)
                //     ->where(function($q) use($request, $city){
                //         $q->orwhere('city_id', $request->city)
                //         ->orwhere('state_id', $city->state_id);
                //     })
                //     ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                // })
                ->take(5);

                if(isset($request->country)){

                    $category->with(['Ads' => function($b) use($latitude, $longitude, $radius, $request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->where(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    }])
                    ->whereHas('Ads',function($b) use($latitude, $longitude, $radius, $request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->where(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    });
                }

                $categoryDefault = $categoryDefault->get()->map(function($a){

                    $a->Subcategory->map(function($c){

                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });

                $otherCategory = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', '!=', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order')
                    ->take(4);
                }]);

                if(isset($request->country)){

                    $otherCategory->with(['Ads' => function($b) use($request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    }])
                    ->whereHas('Ads', function($b) use($request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    });
                }

                $otherCategory = $otherCategory->get()->map(function($a){

                    $a->Subcategory->map(function($c){
                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });

            }
            else{
                
                $category = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order')
                    ->take(4);
                }])
                // ->with(['Ads' => function($b) use($latitude, $longitude, $radius){
                //     $b->where('status', Status::ACTIVE)
                //     ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                //         // ->with(['CustomValue' => function($c){
                //         //     $c->with(['Field' => function($d){
                //         //         $d->where('status', Status::ACTIVE)
                //         //         ->where('delete_status', '!=', Status::DELETE);
                //         //     }]);
                //         // }]);
                // }])
                // ->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                //     $b->where('status', Status::ACTIVE)
                //     ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                // })
                ->take(5);

                if($latitude != 0 && $longitude != 0){

                    $category->with(['Ads' => function($b) use($latitude, $longitude, $radius){
                            $b->where('status', Status::ACTIVE)
                            ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                                sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                                ->having('distance', '<=', $radius);
                        }])
                        ->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                            $b->where('status', Status::ACTIVE)
                            ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                                sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                                ->having('distance', '<=', $radius);
                    });
                }

                if(isset($request->country)){

                    $category->with(['Ads' => function($b) use($latitude, $longitude, $radius, $request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    }])
                    ->whereHas('Ads',function($b) use($latitude, $longitude, $radius, $request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    });
                }

                if(!isset($request->country) && ($latitude == 0 && $longitude == 0)){

                    $category->with(['Ads' => function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', 229);
                    }])
                    ->whereHas('Ads', function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', 229);
                    });
                }

                $category = $category->get()->map(function($a){

                    $a->Subcategory->map(function($c){

                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    $a->Ads->map(function($b){

                        $b->country = $b->Country->name;
                        $b->currency = $b->Country->Currency ? $b->Country->Currency->currency_code : '';
                        $b->state = $b->State->name;
                        $b->city = $b->City ? $b->City->name : $b->State->name ;
                        $b->CustomValue->map(function($c){
                            
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

                        $b->image = array_filter([
                            $b->Image->map(function($q) use($b){
                                $q->image;
                                unset($q->ads_id, $q->img_flag);
                                return $q;
                            }),
                        ]);
                            
                        unset($b->Image->ads_id, $b->Image->img_flag);
                        unset($b->Country, $b->State, $b->City, $b->category_id, $b->subcategory_id, $b->country_id, $b->state_id, $b->city_id, $b->sellerinformation_id, $b->customer_id, $b->payment_id, $b->delete_status, $b->status, $b->reject_reason_id);
                        return $b;
                    });
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });

                $categoryDefault = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order');
                }])
                // ->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                //     $b->where('status', Status::ACTIVE)
                //     ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                // })
                ->take(5);

                if($latitude != 0 && $longitude != 0){

                    $categoryDefault->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                        $b->where('status', Status::ACTIVE)
                        ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                            ->having('distance', '<=', $radius);
                    });
                }

                if(isset($request->country)){
                    $categoryDefault->with(['Ads' => function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    }])
                    ->whereHas('Ads',function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    });
                }

                if(!isset($request->country) && ($latitude == 0 && $longitude == 0)){

                    $categoryDefault->with(['Ads' => function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', 229);
                    }])
                    ->whereHas('Ads', function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', 229);
                    });
                }

                $categoryDefault = $categoryDefault->get()->map(function($a){

                    $a->Subcategory->map(function($c){

                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });

                $otherCategory = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', '!=', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order');
                }]);

                if($latitude != 0 && $longitude != 0){

                    $otherCategory->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                        $b->where('status', Status::ACTIVE)
                        ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                            ->having('distance', '<=', $radius);
                    });
                }

                if(isset($request->country)){
                   
                    $otherCategory->with(['Ads' => function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    }])
                    ->whereHas('Ads',function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    });
                }

                $otherCategory = $otherCategory->get()->map(function($a){

                    $a->Subcategory->map(function($c){
                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });

            }


            return response()->json([
                'status'    => 'success',
                'code'      => 200,
                'data'      => [
                    'loged_user_status' => $user,
                    'user_name'         => $userName,
                    'category_default'  => $categoryDefault,
                    'categories'        => $category,
                    'otherCategory'     => $otherCategory,
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

    public function MenuList(Request $request){

        try{

            $latitude = $request->latitude;
            $longitude = $request->longitude;
            
            $radius = 10; // Km
            $r = 6371000; // earth's mean radius 6371 for kilometer and 3956 for miles
            

            if(isset($request->city)){

                $city = City::where('id', $request->city)
                ->first();
            
                $categoryDefault = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order');
                }])
                // ->whereHas('Ads',function($b) use($latitude, $longitude, $radius, $request, $city){
                //     $b->where('status', Status::ACTIVE)
                //     ->where(function($q) use($request, $city){
                //         $q->orwhere('city_id', $request->city)
                //         ->orwhere('state_id', $city->state_id);
                //     })
                //     ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                // })
                ->take(5);

                if(isset($request->country)){

                    $categoryDefault->with(['Ads' => function($b) use($request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    }])
                    ->whereHas('Ads', function($b) use($request, $city){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country)
                        ->where(function($q) use($request, $city){
                            $q->orwhere('city_id', $request->city)
                            ->orwhere(function($a) use($city){
                                $a->where('city_id', 0)
                                ->where('state_id', $city->state_id);
                            });
                        });
                    });
                }

                $categoryDefault = $categoryDefault->get()->map(function($a){

                    $a->Subcategory->map(function($c){
                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });
            }
            else{
                
                $categoryDefault = Category::where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->where('display_flag', Status::ACTIVE)
                ->orderBy('sort_order')
                ->with(['Subcategory' => function($a){
                    $a->where('delete_status', '!=', Status::DELETE)
                    ->where('status', Status::ACTIVE)
                    ->where('parent_id', 0)
                    ->orderby('sort_order');
                }])
                // ->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                //     $b->where('status', Status::ACTIVE)
                //     ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                //         sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                //         ->having('distance', '<=', $radius);
                // })
                ->take(5);

                if($latitude != 0 && $longitude != 0){

                    $categoryDefault->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                        $b->where('status', Status::ACTIVE)
                        ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                            sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                            ->having('distance', '<=', $radius);
                    });
                }

                if(isset($request->country)){
                   
                    $categoryDefault->with(['Ads' => function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    }])
                    ->whereHas('Ads',function($b) use($request){
                        $b->where('status', Status::ACTIVE)
                        ->where('country_id', $request->country);
                    });
                }

                $categoryDefault = $categoryDefault->get()->map(function($a){

                    $a->Subcategory->map(function($c){
                        $c->SubcategoryChild->map(function($d){

                            unset($d->sort_order, $d->delete_status, $d->status);
                            return $d;
                        });

                        unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                        return $c;
                    });
                    
                    unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                    return $a;
                });
            }


            return response()->json([
                'status'    => 'success',
                'code'      => 200,
                'category'  => $categoryDefault,
            ], 200);
        }
        catch (\Exception $e) {
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getCategory(Request $request){
        
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

        try{

            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $radius = 10; // Km

            $category = Category::where('delete_status', '!=', Status::DELETE)
            ->where('status', Status::ACTIVE)
            ->orderBy('sort_order');
            // ->take(5);

            // if($latitude != 0 && $longitude != 0){
            //     $category->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
            //         $b->where('status', Status::ACTIVE)
            //         ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
            //             sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            //             ->having('distance', '<=', $radius);
            //     });
            // }

            // if(isset($request->country)){

            //     $category->whereHas('Ads',function($b) use($request){
            //         $b->where('status', Status::ACTIVE)
            //         ->where('country_id', $request->country);
            //     });
            // }

            $category = $category->get()
            ->map(function($a){

                unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id, $a->ads);
                return $a;
            });

            return response()->json([

                'status'        => 'success',
                'message'       => 'Category found',
                'code'      => 200,
                'categories'    => $category,
            ], 200);

        }
        catch (\Exception $e) {
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getSubcategory(Request $request){

        $rules = [
            'category'   => 'required|numeric',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid request',
                'code'      => 200,
                'errors'    => $validate->errors(),
            ], 200);
        }

        try{

            $subcategory = Subcategory::where('category_id', $request->category)
            ->where('parent_id', 0)
            ->where('status', Status::ACTIVE)
            ->where('delete_status', '!=', Status::DELETE)
            ->orderBy('sort_order')
            ->with('SubcategoryChild')
            ->get()
            ->map(function($a){
                
                unset($a->delete_status, $a->status, $a->category_id, $a->type, $a->sort_order, $a->percentage);
                return $a;
            });

            return response()->json([

                'status'        => 'success',
                'message'       => 'Category found',
                'code'      => 200,
                'subcategories' => $subcategory,
            ], 200);
        }
        catch (\Exception $e) {
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function getSubSubcategory(Request $request){
        
        try{

            $rules = [
                'category'   => 'required|numeric',
            ];
    
            $validate = Validator::make($request->all(), $rules);
    
            if($validate->fails()){
    
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Invalid request',
                    'code'      => 200,
                    'errors'    => $validate->errors(),
                ], 200);
            }

            $subcategory = Subcategory::where(function($a) use($request){
                $a->orwhere('parent_id', $request->category);
                // ->orwhere('id', $request->category);
            })
            ->where('status', Status::ACTIVE)
            ->where('delete_status', '!=', Status::DELETE)
            // ->orderBy('sort_order')
            // ->with('SubcategoryChild')
            ->get();
            // ->map(function($a){
                
            //     unset($a->delete_status, $a->status, $a->category_id, $a->type, $a->sort_order, $a->percentage);
            //     return $a;
            // });

            return response()->json([
                'status'            => 'success',
                'message'           => 'Category found',
                'code'              => 200,
                'subcategory'       => $subcategory,
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
