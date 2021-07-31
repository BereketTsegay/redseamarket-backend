<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OtherController extends Controller
{
    public function favouriteView(){

        try{

            $favourite = Favorite::where('customer_id', 1)//Auth::user()->id)
            ->get()
            ->map(function($a){

                $a->Ads;
                $a->Ads->image = array_filter([
                    $a->Ads->Image->map(function($q) use($a){
                        $q->image;
                        unset($q->ads_id, $q->img_flag);
                        return $q;
                    }),
                ]);

                $a->Ads->country_name = $a->Ads->Country->name;
                $a->Ads->state_name = $a->Ads->State->name;
                $a->Ads->city_name = $a->Ads->City->name;
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

            return response()->json([
                'status'    => 'success',
                'message'   => 'My favourite ads',
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

            $myAds = Ads::where('customer_id', 1) //Auth::user()->id;
            ->where('status', Status::ACTIVE)
            ->where('delete_status', '!=', Status::DELETE)
            ->get()
            ->map(function($a){

                $a->image = array_filter([
                    $a->Image->map(function($q) use($a){
                        $q->image;
                        unset($q->ads_id, $q->img_flag);
                        return $q;
                    }),
                ]);

                $a->country_name = $a->Country->name;
                $a->state_name = $a->State->name;
                $a->city_name = $a->City->name;
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
                'message'   => 'My ads',
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
                'errors'    => $validate->errors(),
            ], 400);
        }

        try{

            if($request->action == 'store'){

                $favourite              = new Favorite();
                $favourite->ads_id      = $request->ads_id;
                $favourite->customer_id = 1;//Auth::user()->id;
                $favourite->save();

                return response()->json([
                    'status'    => 'success',
                    'message'   => 'Ad added to favourite',
                ], 200);
            }
            else{
                Favorite::where('ads_id', $request->ads_id)
                ->where('customer_id', 1) //Auth::user()->id;
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

            if($request->city && $request->category){

                $myAds = tap(Ads::where('customer_id', 1) //Auth::user()->id;
                ->where(function($a) use($request){
                    $a->orwhere('title', 'like', '%'.$request->search_key.'%')
                    ->orwhere('canonical_name', 'like', '%'.$request->search_key.'%');
                })
                ->where('category_id', $request->category)
                ->where('city_id', $request->city)
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(1), function ($paginatedInstance){
                    return $paginatedInstance->getCollection()->transform(function($a){

                        $a->image = array_filter([
                            $a->Image->map(function($q) use($a){
                                $q->image;
                                unset($q->ads_id, $q->img_flag);
                                return $q;
                            }),
                        ]);

                        $a->country_name = $a->Country->name;
                        $a->state_name = $a->State->name;
                        $a->city_name = $a->City->name;
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

                $myAds = tap(Ads::where('customer_id', 1) //Auth::user()->id;
                ->where(function($a) use($request){
                    $a->orwhere('title', 'like', '%'.$request->search_key.'%')
                    ->orwhere('canonical_name', 'like', '%'.$request->search_key.'%');
                })
                ->where('city_id', $request->city)
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(1), function ($paginatedInstance){
                    return $paginatedInstance->getCollection()->transform(function($a){

                        $a->image = array_filter([
                            $a->Image->map(function($q) use($a){
                                $q->image;
                                unset($q->ads_id, $q->img_flag);
                                return $q;
                            }),
                        ]);

                        $a->country_name = $a->Country->name;
                        $a->state_name = $a->State->name;
                        $a->city_name = $a->City->name;
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

                $myAds = tap(Ads::where('customer_id', 1) //Auth::user()->id;
                ->where(function($a) use($request){
                    $a->orwhere('title', 'like', '%'.$request->search_key.'%')
                    ->orwhere('canonical_name', 'like', '%'.$request->search_key.'%');
                })
                ->where('category_id', $request->category)
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(1), function ($paginatedInstance){
                    return $paginatedInstance->getCollection()->transform(function($a){

                        $a->image = array_filter([
                            $a->Image->map(function($q) use($a){
                                $q->image;
                                unset($q->ads_id, $q->img_flag);
                                return $q;
                            }),
                        ]);

                        $a->country_name = $a->Country->name;
                        $a->state_name = $a->State->name;
                        $a->city_name = $a->City->name;
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

                $myAds = tap(Ads::where('customer_id', 1) //Auth::user()->id;
                ->where(function($a) use($request){
                    $a->orwhere('title', 'like', '%'.$request->search_key.'%')
                    ->orwhere('canonical_name', 'like', '%'.$request->search_key.'%');
                })
                ->where('status', Status::ACTIVE)
                ->where('delete_status', '!=', Status::DELETE)
                ->paginate(1), function ($paginatedInstance){
                    return $paginatedInstance->getCollection()->transform(function($a){

                        $a->image = array_filter([
                            $a->Image->map(function($q) use($a){
                                $q->image;
                                unset($q->ads_id, $q->img_flag);
                                return $q;
                            }),
                        ]);

                        $a->country_name = $a->Country->name;
                        $a->state_name = $a->State->name;
                        $a->city_name = $a->City->name;
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
                'message'   => 'Search result',
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
}
