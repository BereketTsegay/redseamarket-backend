<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcategoryField extends Model
{
    use HasFactory;
    protected $guarded=[];
      public function Field(){
        return $this->belongsTo(Fields::class, 'field_id', 'id')
        ->where('delete_status', '!=', Status::DELETE);
    }
}
