<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'sub_task_id', 'comment'];

    public function subTask()
    {
        return $this->belongsTo(SubTask::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
