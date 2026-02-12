<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskRemark extends Model
{
    protected $fillable = [
        'task_id',
        'remark',
        'user_id',
        'progress',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function files()
    {
        return $this->hasMany(TaskFile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
