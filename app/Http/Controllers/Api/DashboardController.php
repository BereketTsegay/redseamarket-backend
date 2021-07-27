<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function dashboard(Request $request){

        $rules = [
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
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

            if(Auth::user()){
                $user = true;
            }
            else{
                $user = false;
            }

            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $radius = 10; // Km
            $r = 6371000; // earth's mean radius 6371 for kilometer and 3956 for miles
            //id, user_id, name, address_line, city, longitude, latitude, mobile, email, profile_image

            $category = Category::where('delete_status', '!=', Status::DELETE)
            ->where('status', Status::ACTIVE)
            ->orderBy('sort_order')
            ->with(['Subcategory' => function($a){
                $a->where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->orderby('sort_order')
                ->take(4);
            }])
            ->with(['Ads' => function($b) use($latitude, $longitude, $radius){
                $b->where('status', Status::ACTIVE)
                ->selectRaw('*,(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius);
                    // ->with(['CustomValue' => function($c){
                    //     $c->with(['Field' => function($d){
                    //         $d->where('status', Status::ACTIVE)
                    //         ->where('delete_status', '!=', Status::DELETE);
                    //     }]);
                    // }]);
            }])
            ->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                $b->where('status', Status::ACTIVE)
                ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius);
            })
            ->take(5)
            ->get()
            ->map(function($a){

                $a->Subcategory->map(function($c){
                    unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                    return $c;
                });
                
                $a->Ads->map(function($b){

                    $b->country = $b->Country->name;
                    $b->state = $b->State->name;
                    $b->city = $b->City->name;
                    $b->CustomValue->map(function($c){
                        
                        if($c->Field->description_area_flag == 0){
                            $c->position = 'top';
                        }
                        elseif($c->Field->description_area_flag == 1){
                            $c->position = 'details_page';
                        }
                        else{
                            $c->position = 'none';
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
            ->orderBy('sort_order')
            ->with(['Subcategory' => function($a){
                $a->where('delete_status', '!=', Status::DELETE)
                ->where('status', Status::ACTIVE)
                ->orderby('sort_order')
                ->take(4);
            }])
            ->whereHas('Ads',function($b) use($latitude, $longitude, $radius){
                $b->where('status', Status::ACTIVE)
                ->selectRaw('(6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * 
                    sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $radius);
            })
            ->take(5)
            ->get()
            ->map(function($a){

                $a->Subcategory->map(function($c){
                    unset($c->category_id, $c->parent_id, $c->type, $c->status, $c->sort_order, $c->delete_status, $c->percentage);
                    return $c;
                });
                
                unset($a->country_id, $a->state_id, $a->city_id, $a->delete_status, $a->sort_order, $a->status, $a->icon_class_id,);
                return $a;
            });


            return response()->json([
                'status'    => 'success',
                'data'      => [
                    'loged_user_status' => $user,
                    'category_default'  => $categoryDefault,
                    'categories'        => $category,
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
