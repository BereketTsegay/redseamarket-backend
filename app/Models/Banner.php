<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    public function Country(){
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
}
