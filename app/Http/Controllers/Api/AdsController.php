<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Http\Controllers\Controller;
use App\Models\CategoryField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdsController extends Controller
{
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
            ], 401);
        }

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
}
