<?php

namespace App\Models;

use App\Common\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    public function Category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function SubcategoryReverse(){
        return $this->belongsTo(Subcategory::class, 'parent_id', 'id');
    }

    public function CustomField(){
        return $this->hasMany(CategoryField::class, 'subcategory_id', 'id');
    }

    public function Ads(){
        return $this->hasMany(Ads::class, 'subcategory_id', 'id')
        ->where('status', Status::ACTIVE)
        ->where('delete_status', '!=', Status::DELETE);
    }

    public function SubcategoryChild(){
        return $this->hasMany(Subcategory::class, 'parent_id', 'id')
        ->where('status', Status::ACTIVE)
        ->where('delete_status', '!=', Status::DELETE);
    }
}
