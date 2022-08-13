<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelMst extends Model
{
    use HasFactory;
    public function MakeMst(){

        return $this->belongsTo(MakeMst::class, 'make_id', 'id');
    }
    public function VarientMsts(){

        return $this->hasMany(VarientMst::class, 'model_id', 'id');
    }
    
    public function MotorCustomeValues(){

        return $this->hasMany(MotorCustomeValues::class, 'model_id', 'id');
    }
}
