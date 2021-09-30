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
        return $this->hasOne(SellerInformation::class, 'id', 'sellerinformation_id');
    }

    public function AdsFieldDependency(){
        return $this->hasMany(AdsFieldDependency::class, 'ads_id', 'id')
        ->where('delete_status', '!=', Status::DELETE);
    }

    public function MotoreValue(){
        return $this->hasOne(MotorCustomeValues::class, 'ads_id', 'id');
    }

    public function MotorFeatures(){
        return $this->hasMany(MotorFeatures::class, 'ads_id', 'id');
    }

    public function PropertyRend(){
        return $this->hasOne(PropertyRendCustomeValues::class, 'ads_id', 'id');
    }

    public function PropertySale(){
        return $this->hasOne(PropertySaleCustomeValues::class, 'ads_id', 'id');
    }

    public function RejectionNote(){
        return $this->hasOne(RejectReason::class, 'id', 'reject_reason_id');
    }

    public function Favourite(){
        return $this->hasOne(Favorite::class, 'ads_id', 'id');
    }

    public function Payment() {
        return $this->hasOne(Payment::class, 'ads_id', 'id');
    }

    public function Currency(){
        return $this->hasOne(CurrencyCode::class, 'id', 'country_id');
    }
}
