<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorCustomeValues extends Model
{
    use HasFactory;

    public function Make(){
        return $this->hasOne(MakeMst::class, 'id', 'make_id');
    }

    public function Model(){
        return $this->hasOne(ModelMst::class, 'id', 'model_id');
    }

    public function Variant(){
        return $this->hasOne(VarientMst::class, 'id', 'varient_id');
    }
}
