<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    use HasFactory;

    protected $fillable = [
        'project_id',
        'task_type',
        'assigned_user_id',
        'start_date',
        'due_date',
        'progress',
        'created_by',
        'archived_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date'   => 'date',
        'archived_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function latestRemark()
    {
        return $this->hasOne(TaskRemark::class)
            ->where(function ($query) {
                $query->whereNotNull('remark')
                    ->where('remark', '!=', '');
            })
            ->orderByDesc('id');
    }

    public function remarks()
    {
        return $this->hasMany(TaskRemark::class)
            ->latest();
    }

    public function files()
    {
        return $this->hasMany(TaskFile::class)
            ->latest();
    }
}
