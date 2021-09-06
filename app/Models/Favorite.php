<?php

namespace App\Models;

use App\Common\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    public function Ads(){
        return $this->hasOne(Ads::class, 'id', 'ads_id')
        ->where('status', Status::ACTIVE)
        ->where('delete_status', Status::UNDELETE);
    }
}
