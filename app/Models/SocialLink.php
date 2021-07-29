<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Polyfill\Iconv\Iconv;

class SocialLink extends Model
{
    use HasFactory;

    public function Icon(){
        return $this->belongsTo(IconClass::class, 'icon', 'id');
    }
}
