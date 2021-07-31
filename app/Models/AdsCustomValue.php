<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsCustomValue extends Model
{
    use HasFactory;

    public function Field(){
        return $this->hasOne(Fields::class, 'id', 'field_id');
    }

    public function Option(){
        return $this->hasOne(FieldOptions::class, 'id', 'option_id');
    }
}
