<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskActivityLog extends Model
{

    protected $casts = [
        'changes' => 'array',
    ];

    protected $fillable = [
        'task_id',
        'user_id',
        'action',
        'description',
        'changes',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
