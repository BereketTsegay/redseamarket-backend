<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobProfile extends Model
{
    use HasFactory;

    public function Company(){
        return $this->hasMany(JobProfileCompany::class, 'job_profile_id', 'id');
    }

    public function User(){
        return $this->belongsTo(User::class, 'user_id', 'id');
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
}
