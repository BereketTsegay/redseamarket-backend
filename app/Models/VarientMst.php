<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VarientMst extends Model
{
    use HasFactory;
    
    public function ModelMst(){

        return $this->belongsTo(ModelMst::class, 'model_id', 'id');
    }
    public function MotorCustomeValues()
    {

        return $this->hasMany(MotorCustomeValues::class, 'varient_id', 'id');
    }
}
