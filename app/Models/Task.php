<?php

namespace App\Models;

use App\Common\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public function TaskRole(){

        return $this->belongsTo(TaskRoleMapping::class, 'id', 'task_id');
    }
}
