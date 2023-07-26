<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobProfile extends Model
{
    use HasFactory;

    public function Company(){
        return $this->hasMany(JobProfileCompany::class, 'job_profile_id', 'id')->orderBy('from_date','DESC');
    }
}
