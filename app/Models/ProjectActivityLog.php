<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectActivityLog extends Model
{
    protected $casts = [
        'changes' => 'array',
    ];

    protected $fillable = [
        'project_id',
        'user_id',
        'action',
        'description',
        'changes',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
