<?php

namespace App\Models;

use App\Common\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    use HasFactory;

    public function Category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function User(){
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function Subcategory(){
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id');
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

    public function Image(){
        return $this->hasMany(AdsImage::class, 'ads_id', 'id');
    }

    public function CustomValue(){
        return $this->hasMany(AdsCustomValue::class, 'ads_id', 'id');
    }

    public function SellerInformation(){
        return $this->hasOne(SellerInformation::class);
    }

    public function AdsFieldDependency(){
        return $this->hasMany(AdsFieldDependency::class, 'ads_id', 'id')
        ->where('delete_status', '!=', Status::DELETE);
    }
}
