<?php

namespace App\Models;

use App\Common\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fields extends Model
{
    use HasFactory;

    public function CategoryField(){
        return $this->hasMany(CategoryField::class, 'field_id', 'id')
        ->where('delete_status', '!=', Status::DELETE);
    }

    public function FieldOption(){
        return $this->hasMany(FieldOptions::class, 'field_id', 'id')
        ->where('status', Status::ACTIVE)
        ->where('delete_status', '!=', Status::DELETE);
    }

    public function Dependency(){
        return $this->hasMany(CustomFieldDependancy::class, 'field_id', 'id')
        ->where('delete_status', '!=', Status::DELETE)
        ->orderBy('order');
    }
    public function getSubcategoryIdAsArray() {
        $subcategorys=SubcategoryField::where('field_id',$this->id)->where('status',1)->get();
        $values=[];
        foreach ($subcategorys as $subcategoryfield)
        {
            $values[]=$subcategoryfield->subcategory_id;
        }
        return $values;
    }
}
