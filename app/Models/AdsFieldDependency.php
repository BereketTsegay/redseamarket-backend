<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsFieldDependency extends Model
{
    use HasFactory;

    public function Make(){
        return $this->belongsTo(MakeMst::class, 'master_id', 'id');
    }

    public function Model(){
        return $this->belongsTo(ModelMst::class, 'master_id', 'id');
    }

    public function Variant(){
        return $this->belongsTo(VarientMst::class, 'master_id', 'id');
    }

    public function Country(){
        return $this->belongsTo(Country::class, 'master_id', 'id');
    }

    public function State(){
        return $this->belongsTo(State::class, 'master_id', 'id');
    }

    public function City(){
        return $this->belongsTo(City::class, 'master_id', 'id');
    }
}
