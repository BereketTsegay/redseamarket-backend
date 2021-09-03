<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\CustomFieldDependancy;
use App\Models\Dependency;
use App\Models\FieldOptions;
use App\Models\Fields;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index(){

        $field = Fields::where('delete_status', '!=', Status::DELETE)
        ->orderBy('created_at', 'desc')
        ->get();
        
        $category = Category::where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE)
        ->orderBy('sort_order')
        ->get();

        return view('ads.custom_field.custom_field', compact('field', 'category'));
    }

    public function create(){

        return view('ads.custom_field.create_custom_field');
    }

    public function store(Request $request){
        
        $request->validate([
            'name'              => 'required',
            'type'              => 'required',
            'description_area'  => 'required',
            'field_length'      => 'required',
        ]);

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        if($request->required == 'checked'){
            $required = 1;
        }
        else{
            $required = 0;
        }

        $typ = explode('-', $request->type);

        if(count($typ) != 1){
            if($typ[1] == 2){
                $type = $typ[0];
                $option = 2;
            }
            else{
                $type = $typ[0];
                $option = 1;
            }
        }
        else{
            $type = $request->type;
            $option = 0;
        }

        if($request->name){
            $subName = explode(' ', $request->name);

            if(count($subName) > 0){
                $name = '';
                $k = 0;
                foreach($subName as $nameRow){

                    if($k == 0){
                        $name = $name.$nameRow;
                    }
                    else{
                        $name = $name.'-'.$nameRow;
                    }
                    $k++;
                }
            }
            else{
                $name = $request->name;
            }
        }

        $field                          = new Fields();
        $field->name                    = $name;
        $field->type                    = $type;
        $field->max                     = $request->field_length;
        $field->default_value           = $request->default_value;
        $field->description_area_flag   = $request->description_area;
        $field->required                = $required;
        $field->status                  = $status;
        $field->option                  = $option;
        $field->save();

        if($request->dependency_select == 'Country'){

            $customFieldDependancy              = new CustomFieldDependancy();
            $customFieldDependancy->field_id    = $field->id;
            $customFieldDependancy->master      = 'Country';
            $customFieldDependancy->order       = 1;
            $customFieldDependancy->save();

            if($request->State){

                $customFieldDependancy              = new CustomFieldDependancy();
                $customFieldDependancy->field_id    = $field->id;
                $customFieldDependancy->master      = 'State';
                $customFieldDependancy->order       = 2;
                $customFieldDependancy->save();
            }
            if($request->City){
                $customFieldDependancy              = new CustomFieldDependancy();
                $customFieldDependancy->field_id    = $field->id;
                $customFieldDependancy->master      = 'City';
                $customFieldDependancy->order       = 3;
                $customFieldDependancy->save();
            }
        }
        elseif($request->dependency_select == 'Make'){
            
            $customFieldDependancy              = new CustomFieldDependancy();
            $customFieldDependancy->field_id    = $field->id;
            $customFieldDependancy->master      = 'Make';
            $customFieldDependancy->order       = 1;
            $customFieldDependancy->save();

            if($request->Model){
                $customFieldDependancy              = new CustomFieldDependancy();
                $customFieldDependancy->field_id    = $field->id;
                $customFieldDependancy->master      = 'Model';
                $customFieldDependancy->order       = 2;
                $customFieldDependancy->save();
            }

            if($request->Variant){
                $customFieldDependancy              = new CustomFieldDependancy();
                $customFieldDependancy->field_id    = $field->id;
                $customFieldDependancy->master      = 'Variant';
                $customFieldDependancy->order       = 3;
                $customFieldDependancy->save();
            }
        }
        

        session()->flash('success', 'Custom field has been stored');
        return redirect()->route('custom_field.index');
    }

    public function view($id){

        $field = Fields::where('id', $id)
        ->first();

        return view('ads.custom_field.custom_field_details', compact('field'));
    }

    public function edit($id){

        $field = Fields::where('id', $id)
        ->first();

        return view('ads.custom_field.edit_custom_field', compact('field'));
    }

    public function update(Request $request, $id){

        $request->validate([
            'name'              => 'required',
            'type'              => 'required',
            'description_area'  => 'required',
            'field_length'      => 'required',
        ]);

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        if($request->required == 'checked'){
            $required = 1;
        }
        else{
            $required = 0;
        }

        $typ = explode('-', $request->type);

        if(count($typ) != 1){
            if($typ[1] == 2){
                $type = $typ[0];
                $option = 2;
            }
            else{
                $type = $typ[0];
                $option = 1;
            }
        }
        else{
            $type = $request->type;
            $option = 0;
        }

        if($request->name){
            $subName = explode(' ', $request->name);

            if(count($subName) > 0){
                $name = '';

                $k = 0;
                foreach($subName as $nameRow){

                    if($k == 0){
                        $name = $name.$nameRow;
                    }
                    else{
                        $name = $name.'-'.$nameRow;
                    }
                    $k++;
                }
            }
            else{
                $name = $request->name;
            }
        }

        Fields::where('id', $id)
        ->update([
            'name'                  => $name,
            'type'                  => $type,
            'description_area_flag' => $request->description_area,
            'max'                   => $request->field_length,
            'default_value'         => $request->default_value,
            'required'              => $required,
            'status'                => $status,
            'option'                => $option,
        ]);

        if($request->dependency_select == 'Country'){

            $customFieldDependancy              = new CustomFieldDependancy();
            $customFieldDependancy->field_id    = $id;
            $customFieldDependancy->master      = 'Country';
            $customFieldDependancy->order       = 1;
            $customFieldDependancy->save();

            if($request->State){

                $customFieldDependancy              = new CustomFieldDependancy();
                $customFieldDependancy->field_id    = $id;
                $customFieldDependancy->master      = 'State';
                $customFieldDependancy->order       = 2;
                $customFieldDependancy->save();
            }
            if($request->City){
                $customFieldDependancy              = new CustomFieldDependancy();
                $customFieldDependancy->field_id    = $id;
                $customFieldDependancy->master      = 'City';
                $customFieldDependancy->order       = 3;
                $customFieldDependancy->save();
            }
        }
        elseif($request->dependency_select == 'Make'){
            
            $customFieldDependancy              = new CustomFieldDependancy();
            $customFieldDependancy->field_id    = $id;
            $customFieldDependancy->master      = 'Make';
            $customFieldDependancy->order       = 1;
            $customFieldDependancy->save();

            if($request->Model){
                $customFieldDependancy              = new CustomFieldDependancy();
                $customFieldDependancy->field_id    = $id;
                $customFieldDependancy->master      = 'Model';
                $customFieldDependancy->order       = 2;
                $customFieldDependancy->save();
            }

            if($request->Variant){
                $customFieldDependancy              = new CustomFieldDependancy();
                $customFieldDependancy->field_id    = $id;
                $customFieldDependancy->master      = 'Variant';
                $customFieldDependancy->order       = 3;
                $customFieldDependancy->save();
            }
        }

        session()->flash('success', 'Custom field has been updated');
        return redirect()->route('custom_field.index');
    }

    public function delete($id){
        
        Fields::where('id', $id)
        ->update([
            'delete_status' => Status::DELETE,
        ]);

        session()->flash('success', 'Custom field has been deleted');
        return redirect()->route('custom_field.index');
    }

    public function customDependencyDelete($id){

        CustomFieldDependancy::where('id', $id)
        ->update([
            'delete_status' => Status::DELETE,
        ]);

        session()->flash('success', 'Custom field dependency has been deleted');

        return redirect()->back();
    }

    public function optionIndex($id){

        $option = FieldOptions::where('field_id', $id)
        ->where('delete_status', '!=', Status::DELETE)
        ->orderBy('created_at', 'desc')
        ->get();

        $field = Fields::where('id', $id)
        ->first();

        return view('ads.custom_field.option', compact('option', 'field'));
    }

    public function optionCreate(Request $request, $id){

        $request->validate([
            'value' => 'required',
        ]);

        $subValue = explode(' ', $request->value);

        $k = 0;
        $newValue = '';

        foreach($subValue as $value){

            if($k == 0){
                
                $newValue = $newValue.$value;
                
            }
            else{
                $newValue = $newValue.'-'.$value;
            }
            $k++;
        }

        $option             = new FieldOptions();
        $option->field_id   = $id;
        $option->value      = $newValue;
        $option->save();

        session()->flash('success', 'Option has been stored');
        return redirect()->back();
    }

    public function optionDelete($id){

        FieldOptions::where('id', $id)
        ->update([
            'delete_status' => Status::DELETE,
        ]);

        session()->flash('success', 'Option has been deleted');
        return redirect()->back();
    }

    public function addtoCategory(Request $request){
        
        $request->validate([
            'category'  => 'required',
        ]);

        if($request->disabled == 'checked'){
            
            $disabled = 1;
        }
        else{
            $disabled = 0;
        }

        $category = explode('_', $request->category);

        if($category[0] == 'category'){

            $category_id    = $category[1];
            $subcategory_id = 0;
        }
        else{
            $category_id    = $category[1];
            $subcategory_id = $category[2];
        }

        $cateField = CategoryField::where('category_id', $category_id)
        ->where('field_id', $request->field_id);

        if($subcategory_id != 0 ){
            $cateField->where('subcategory_id', $subcategory_id);
        }

        $cateField = $cateField->first();

        if($cateField){

            session()->flash('warning', 'Category has been already assigned');
            return redirect()->route('custom_field.index');
        }

        $field = Fields::where('id', $request->field_id)
        ->first();

        if($field->type == 'radio' || $field->type == 'select'){

            if(count($field->FieldOption) <= 0){

                session()->flash('warning', 'Create atleast on option for this field');
                return redirect()->route('custom_field.index');
            }
        }

        $categoryField                              = new CategoryField();
        $categoryField->category_id                 = $category_id;
        $categoryField->subcategory_id              = $subcategory_id;
        $categoryField->field_id                    = $request->field_id;
        $categoryField->disabled_in_sub_category    = $disabled;
        $categoryField->save();

        session()->flash('success', 'Category has been assigned');
        return redirect()->route('custom_field.index');
    }

    public function deleteFromCategory($id){

        CategoryField::where('id', $id)
        ->update([
            'delete_status' => Status::DELETE,
        ]);

        session()->flash('success', 'Custom field has been deleted');
        return redirect()->back();
    }

    public function dependencyGet(){

        $dependency = Dependency::where('dependency_table', null)
        ->get();

        return response()->json($dependency);
    }

    public function dependencyGetDependent(Request $request){

        $dependency = Dependency::where('dependency_table', '!=', null)
        ->where('dependency_table', $request->data)
        ->get();

        return response()->json($dependency);
    }
}
