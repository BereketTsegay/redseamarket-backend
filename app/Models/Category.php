<?php

namespace App\Models;

use App\Common\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function Subcategory(){

        return $this->hasMany(Subcategory::class, 'category_id', 'id')
        ->where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE);
    }

    public function Icon(){
        return $this->hasOne(IconClass::class, 'id', 'icon_class_id')
        ->where('delete_status', '!=', Status::DELETE)
        ->where('status', Status::ACTIVE);
    }

    public function CustomField(){
        return $this->hasMany(CategoryField::class, 'category_id', 'id')
        ->where('delete_status', '!=', Status::DELETE);
    }

    public function Country(){
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function State(){

        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function City(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function Ads(){
        return $this->hasMany(Ads::class, 'category_id', 'id')
        ->where('delete_status', '!=', Status::DELETE);
    }
}
