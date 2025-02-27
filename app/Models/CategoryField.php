<?php

namespace App\Models;

use App\Common\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryField extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function Field(){
        return $this->belongsTo(Fields::class, 'field_id', 'id')
        ->where('delete_status', '!=', Status::DELETE);
    }
    public function Category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')
        ->where('delete_status', '!=', Status::DELETE);
    }
}
