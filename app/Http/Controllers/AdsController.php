<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\Ads;
use App\Models\AdsCustomValue;
use App\Models\AdsFieldDependency;
use App\Models\AdsImage;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\Country;
use App\Models\FieldOptions;
use App\Models\MakeMst;
use App\Models\RejectReason;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdsController extends Controller
{
    public function index(){

        $ad = Ads::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->paginate(10);

        return view('ads.ad_list', compact('ad'));
    }

    public function create(){
        
        $category = Category::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->get();

        $country = Country::orderBy('name')
        ->get();

        return view('ads.create_ad', compact('category', 'country'));
    }

    public function store(Request $request){

        $request->validate([
            'category'          => 'required|numeric',
            'title'             => 'required',
            'price'             => 'required|numeric',
            'state'             => 'required|numeric',
            'sort_order'        => 'required',
            // 'subcategory'       => 'numeric',
            'canonical_name'    => 'required',
            'country'           => 'required|numeric',
            // 'city'              => 'required|numeric',
            'description'       => 'required',
            'image.*'           => 'required|mimes:png,jpg,jpeg',
        ]);
        
        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        if($request->negotiable == 'checked'){
            $negotiable_flag = 1;
        }
        else{
            $negotiable_flag = 0;
        }

        if($request->featured == 'checked'){
            $featured_flag = 1;
        }
        else{
            $featured_flag = 0;
        }

        if($request->city){
            $city = $request->city;
        }
        else{
            $city = $request->state;
        }
        
        $categoryField = CategoryField::where('category_id', $request->category)
        ->with(['Field' => function($a){
            $a->where('delete_status', '!=', Status::DELETE)
            ->with(['FieldOption' => function($q){
                $q->where('delete_status', '!=', Status::DELETE);
            }]);
        }])
        ->get();

        $ad                         = new Ads();
        $ad->category_id            = $request->category;
        $ad->subcategory_id         = $request->subcategory;
        $ad->title                  = $request->title;
        $ad->canonical_name         = $request->canonical_name;
        $ad->description            = $request->description;
        $ad->price                  = $request->price;
        $ad->negotiable_flag        = $negotiable_flag;
        $ad->country_id             = $request->country;
        $ad->state_id               = $request->state;
        $ad->city_id                = $city;
        $ad->sellerinformation_id   = 0; //Admin
        $ad->customer_id            = Auth()->user()->id;
        $ad->featured_flag          = $featured_flag;
        $ad->latitude               = $request->address_latitude;
        $ad->longitude              = $request->address_longitude;
        $ad->status                 = $status;
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

                $fieldOption = FieldOptions::where('id', $optionValue)
                ->where('field_id', $catRow->field_id)
                ->first();

                $option_id = $fieldOption->id;

                $customValue = new AdsCustomValue();
                $customValue->ads_id    = $ad->id;
                $customValue->field_id  = $catRow->field_id;
                $customValue->option_id = $option_id;
                $customValue->value     = $optionValue;
                $customValue->save();

            }
            elseif($catRow->Field->type == 'checkbox_multiple'){

                foreach($catRow->Field->FieldOption as $fieldOptionRow){

                    if($request->$fieldOptionRow->value == 'checked'){

                        $customValue = new AdsCustomValue();
                        $customValue->ads_id    = $ad->id;
                        $customValue->field_id  = $catRow->field_id;
                        $customValue->option_id = $fieldOptionRow->id;
                        $customValue->value     = $fieldOptionRow->value;
                        $customValue->save();
                    }
                }
            }
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

                $date = Carbon::createFromFormat('d/m/Y', $request->$field_name)->format('Y-m-d');

                $customValue = new AdsCustomValue();
                $customValue->ads_id    = $ad->id;
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
                $customValue->ads_id    = $ad->id;
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

        session()->flash('success', 'Ad has been created');
        return redirect()->route('ads.index');
    }

    public function getCustomField(Request $request){

        $field = CategoryField::where('category_id', $request->id)
        ->with(['Field' => function($a){
            $a->where('delete_status', '!=', Status::DELETE)
            ->where(function($b){
                $b->orwhere(function($c){
                    $c->where('option', 1)
                    ->whereHas('FieldOption', function($e){
                        $e->where('delete_status', '!=', Status::DELETE)
                        ->where('status', Status::ACTIVE);
                    });
                })
                ->orwhere(function($d){
                    $d->where('option', 2)
                    ->wherehas('Dependency', function($f){
                        $f->where('delete_status', '!=', Status::DELETE);
                    });
                })
                ->orwhere(function($d){
                    $d->where('option', 0);
                });
            });
            // ->with(['FieldOption' => function($q){
            //     $q->where('delete_status', '!=', Status::DELETE);
            // }])
            // ->with(['Dependency' => function($r){
            //     $r->where('delete_status', '!=', Status::DELETE)
            //     ->orderBy('order')
            //     ->groupBy('field_id');
            // }]);
        }])
        ->get()
        ->map(function($p){
            
            if($p->Field){
                if($p->Field->option == 1){
                    $p->Field->FieldOption;
                }
                elseif($p->Field->option == 2){
                    $p->Field->Dependency;
                }
                else{
                    $p->Field;
                }
            }

            return $p;
        });
        
        return response()->json($field);
    }

    public function view($id){

        $ad = Ads::where('id', $id)
        ->first();
        
        return view('ads.ad_details', compact('ad'));
    }

    public function getMasterDependency(Request $request){

        if($request->master == 'Make'){

            $dependency = MakeMst::where('status', Status::ACTIVE)
            ->orderBy('sort_order')
            ->get();
        }
        elseif($request->master == 'Country'){

            $dependency = Country::orderBy('name')
            ->get();
        }

        return response()->json($dependency);
    }

    public function adAccept($id){
        
        Ads::where('id', $id)
        ->update([
            'status'    => Status::ACTIVE,
        ]);

        session()->flash('success', 'Ad has been accepted');
        return redirect()->route('ads.index');
    }

    public function getRejectReson(Request $request){

        $reason = RejectReason::where('id', $request->id)
        ->first();

        return response()->json($reason);
    }

    public function adRequestIndex(){

        $adsRequest = Ads::where('status', Status::REQUEST)
        ->orderBy('created_at', 'desc')
        ->paginate(1);

        $reason = RejectReason::where('status', Status::ACTIVE)
        ->orderBy('reson')
        ->get();

        return view('ads.request.ad_request', compact('adsRequest', 'reason'));
    }

    public function adRequestDetails($id){

        $ad = Ads::where('id', $id)
        ->first();

        $reason = RejectReason::where('status', Status::ACTIVE)
        ->orderBy('reson')
        ->get();

        return view('ads.request.request_details', compact('ad', 'reason'));
    }
}
