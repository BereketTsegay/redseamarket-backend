<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoleMapping extends Model
{
    use HasFactory;

    public function Role(){
        return $this->hasOne(Roles::class, 'id', 'role_id');
    }
}
