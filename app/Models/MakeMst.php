<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeMst extends Model
{
    use HasFactory;
    public function ModelMsts(){

        return $this->hasMany(ModelMst::class, 'make_id', 'id');
    }
    public function MotorCustomeValues(){

        return $this->hasMany(MotorCustomeValues::class, 'make_id', 'id');
    }

}
