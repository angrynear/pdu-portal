<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFile extends Model
{
    protected $fillable = [
        'task_id',
        'task_remark_id',
        'file_path',
        'original_name',
        'uploaded_by',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function remark()
    {
        return $this->belongsTo(TaskRemark::class, 'task_remark_id');
    }
}
