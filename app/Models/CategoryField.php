<?php

namespace App\Models;

use App\Common\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryField extends Model
{
    use HasFactory;

    public function Field(){
        return $this->belongsTo(Fields::class, 'field_id', 'id')
        ->where('delete_status', '!=', Status::DELETE);
    }
}
