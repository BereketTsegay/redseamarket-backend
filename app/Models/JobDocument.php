<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDocument extends Model
{
    use HasFactory;

    public function Ad() {
        return $this->hasOne(Ads::class,'id','ads_id');
    }
    public function requestCount($id){
        return JobDocument::where('ads_id',$id)->count();
    }
}
